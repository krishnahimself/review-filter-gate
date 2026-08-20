<?php

use App\Mail\ReviewAlert;
use App\Models\Review;
use Illuminate\Support\Facades\Mail;
use Inertia\Testing\AssertableInertia as Assert;

test('the review page renders', function () {
    $this->get(route('home'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page->component('Review'));
});

test('promoter reviews are stored and redirected to Google reviews', function (int $score) {
    $response = $this->post(route('reviews.store'), [
        'score' => $score,
        'feedback' => 'Loved it!',
    ]);

    $response->assertRedirect(config('review.google_review_url'));

    $this->assertDatabaseHas('reviews', [
        'score' => $score,
        'feedback' => 'Loved it!',
    ]);
})->with([4, 5]);

test('promoter reviews do not email the admin', function () {
    Mail::fake();

    $this->post(route('reviews.store'), ['score' => 4]);

    Mail::assertNothingSent();
});

test('detractor reviews are stored and emailed to the admin', function (int $score) {
    Mail::fake();

    $response = $this->post(route('reviews.store'), [
        'score' => $score,
        'feedback' => 'Not great.',
    ]);

    $response->assertRedirect(route('home'));

    $this->assertDatabaseHas('reviews', [
        'score' => $score,
        'feedback' => 'Not great.',
    ]);

    Mail::assertQueued(ReviewAlert::class, function (ReviewAlert $mail) use ($score) {
        return $mail->hasTo(config('review.admin_email'))
            && $mail->review->score === $score;
    });
})->with([1, 2, 3]);

test('the alert email is rendered from the markdown template', function () {
    $review = Review::factory()->create([
        'score' => 2,
        'feedback' => "Line one\nLine two",
    ]);

    $html = (new ReviewAlert($review))->render();

    expect($html)
        ->toContain('New 2-star review')
        ->toContain('2 / 5')
        ->toContain('Line one')
        ->toContain('Line two');
});

test('feedback is optional', function () {
    Mail::fake();

    $this->post(route('reviews.store'), ['score' => 3])
        ->assertRedirect(route('home'));

    $review = Review::query()->sole();

    expect($review->score)->toBe(3)
        ->and($review->feedback)->toBeNull();

    Mail::assertQueued(ReviewAlert::class);
});

test('the score is required', function () {
    $this->post(route('reviews.store'), ['score' => null])
        ->assertSessionHasErrors('score');

    $this->assertDatabaseCount('reviews', 0);
});

test('the score must be an integer between one and five', function (int|string $score) {
    $this->post(route('reviews.store'), ['score' => $score])
        ->assertSessionHasErrors('score');

    $this->assertDatabaseCount('reviews', 0);
})->with([0, 6, 99, 'abc']);

test('feedback must not exceed 5000 characters', function () {
    $this->post(route('reviews.store'), [
        'score' => 4,
        'feedback' => str_repeat('a', 5001),
    ])->assertSessionHasErrors('feedback');
});

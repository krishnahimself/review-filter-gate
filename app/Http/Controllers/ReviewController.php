<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReviewStoreRequest;
use App\Mail\ReviewAlert;
use App\Models\Review;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class ReviewController extends Controller
{
    public function store(ReviewStoreRequest $request): SymfonyResponse
    {
        $review = Review::create($request->validated());

        if ($review->score >= 4) {
            return Inertia::location(config('review.google_review_url'));
        }

        Mail::to(config('review.admin_email'))->queue(new ReviewAlert($review));

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Thanks for your feedback!')]);

        return to_route('home');
    }
}

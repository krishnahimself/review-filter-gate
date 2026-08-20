<x-mail::message>
# New {{ $review->score }}-star review

You received a new review from your website.

**Score:** {{ $review->score }} / 5

@if ($review->feedback)
**Feedback:**

{{ $feedback }}
@else
No feedback was provided.
@endif

<x-mail::button :url="config('review.google_review_url')">
View your Google listing
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>

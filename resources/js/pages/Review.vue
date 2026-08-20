<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { HeartHandshake, PartyPopper } from '@lucide/vue';
import { computed, ref, watch } from 'vue';
import InputError from '@/components/InputError.vue';
import RatingScale from '@/components/review/RatingScale.vue';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { Toaster } from '@/components/ui/sonner';
import { Spinner } from '@/components/ui/spinner';
import { Textarea } from '@/components/ui/textarea';
import { store } from '@/routes/reviews';

const form = useForm<{ score: number | null; feedback: string }>({
    score: null,
    feedback: '',
});

const submitted = ref(false);
const submittedScore = ref<number | null>(null);
const ratingScale = ref<InstanceType<typeof RatingScale> | null>(null);

watch(
    () => form.score,
    () => form.clearErrors('score'),
);

const scoreCopy = computed(() => {
    switch (form.score) {
        case 1:
            return "We're sorry to hear that. Tell us what went wrong and we'll make it right.";
        case 2:
            return 'Thanks for letting us know. What could we do better?';
        case 3:
            return 'Thanks for your honesty. What would it take to earn a higher score?';
        case 4:
            return "We're glad to hear it! What did you enjoy most?";
        case 5:
            return "That's wonderful! We'd love to hear what made the difference.";
        default:
            return 'It takes less than a minute — and we read every response.';
    }
});

const feedbackPlaceholder = computed(() => {
    switch (form.score) {
        case 1:
        case 2:
            return 'What went wrong? We read everything.';
        case 3:
            return 'What could we do better?';
        case 4:
        case 5:
            return 'What did you enjoy most?';
        default:
            return 'Any detail helps us improve.';
    }
});

const thanksCopy = computed(() =>
    (submittedScore.value ?? 0) <= 2
        ? {
              title: 'Thank you for your honesty.',
              body: 'Your feedback will help us make things right.',
          }
        : {
              title: 'Thank you!',
              body: "You've just made our day — and our product better.",
          },
);

const thanksIcon = computed(() =>
    (submittedScore.value ?? 0) <= 2 ? HeartHandshake : PartyPopper,
);

function submit(): void {
    if (form.score === null) {
        form.setError('score', 'Please choose a rating.');
        ratingScale.value?.focusError();

        return;
    }

    form.post(store.post().url, {
        preserveState: true,
        onSuccess: () => {
            submittedScore.value = form.score;
            submitted.value = true;
            form.reset();
        },
    });
}

function startOver(): void {
    submitted.value = false;
    submittedScore.value = null;
    form.reset();
}
</script>

<template>
    <Head title="Share your feedback" />

    <Toaster />

    <div>
        <div
            aria-hidden="true"
            class="fixed inset-0 -z-10 overflow-hidden bg-linear-to-br from-(--review-gradient-a) via-(--review-gradient-b) to-(--review-gradient-c)"
        >
            <div
                class="review-blob absolute -top-24 -left-24 size-80 rounded-full bg-(--review-blob-a) opacity-60 blur-3xl sm:size-96 dark:opacity-50"
            ></div>
            <div
                class="review-blob absolute top-1/3 -right-32 size-96 rounded-full bg-(--review-blob-b) opacity-60 blur-3xl dark:opacity-50"
            ></div>
            <div
                class="review-blob absolute -bottom-32 left-1/4 size-96 rounded-full bg-(--review-blob-c) opacity-60 blur-3xl dark:opacity-50"
            ></div>
        </div>

        <main class="grid min-h-dvh place-items-center px-4 py-12 sm:px-6">
            <section
                class="w-full max-w-xl rounded-3xl border border-border/60 bg-card/70 p-8 shadow-(--review-glow) backdrop-blur-xl motion-safe:animate-in motion-safe:duration-500 motion-safe:fade-in-0 motion-safe:slide-in-from-bottom-4 sm:p-10 dark:bg-card/60"
            >
                <form v-if="!submitted" novalidate @submit.prevent="submit">
                    <h1
                        id="rating-question"
                        class="text-2xl font-bold tracking-tight text-balance sm:text-3xl"
                    >
                        How likely are you to recommend us to your friends and
                        family?
                    </h1>

                    <RatingScale
                        ref="ratingScale"
                        v-model="form.score"
                        :error="form.errors.score"
                        :disabled="form.processing"
                        class="mt-8"
                    />

                    <p
                        class="mt-3 text-sm leading-relaxed text-muted-foreground"
                        aria-live="polite"
                    >
                        {{ scoreCopy }}
                    </p>

                    <div class="mt-8">
                        <div class="flex items-baseline justify-between gap-3">
                            <Label for="feedback">
                                Can you give us any feedback?
                            </Label>
                            <span class="text-xs text-muted-foreground">
                                Optional
                            </span>
                        </div>

                        <Textarea
                            id="feedback"
                            v-model="form.feedback"
                            :placeholder="feedbackPlaceholder"
                            :disabled="form.processing"
                            rows="4"
                            maxlength="5000"
                            class="mt-3"
                        />

                        <div aria-live="polite">
                            <InputError
                                :message="form.errors.feedback"
                                class="mt-2"
                            />
                        </div>
                    </div>

                    <Button
                        type="submit"
                        size="lg"
                        :disabled="form.processing"
                        class="mt-8 w-full bg-linear-to-r from-(--review-accent-a) to-(--review-accent-b) text-white shadow-(--review-accent-glow) hover:opacity-90 active:scale-[0.99]"
                    >
                        <Spinner v-if="form.processing" />
                        {{
                            form.processing ? 'Submitting…' : 'Submit feedback'
                        }}
                    </Button>
                </form>

                <div
                    v-else
                    role="status"
                    aria-live="polite"
                    class="flex flex-col items-center py-4 text-center motion-safe:animate-in motion-safe:duration-300 motion-safe:fade-in-0 motion-safe:zoom-in-95"
                >
                    <div
                        class="flex size-16 items-center justify-center rounded-2xl bg-linear-to-br from-(--review-accent-a) to-(--review-accent-b) text-white shadow-(--review-accent-glow)"
                    >
                        <component :is="thanksIcon" class="size-8" />
                    </div>

                    <h2
                        class="mt-6 text-2xl font-bold tracking-tight text-balance sm:text-3xl"
                    >
                        {{ thanksCopy.title }}
                    </h2>

                    <p
                        class="mt-3 max-w-sm text-sm leading-relaxed text-muted-foreground"
                    >
                        {{ thanksCopy.body }}
                    </p>

                    <Button
                        type="button"
                        variant="ghost"
                        class="mt-8"
                        @click="startOver"
                    >
                        Submit another response
                    </Button>
                </div>
            </section>
        </main>
    </div>
</template>

<style scoped>
.review-blob {
    animation: review-blob-drift 24s ease-in-out infinite;
}

.review-blob:nth-child(2) {
    animation-delay: -8s;
    animation-duration: 28s;
}

.review-blob:nth-child(3) {
    animation-delay: -16s;
    animation-duration: 32s;
}

@keyframes review-blob-drift {
    0%,
    100% {
        transform: translate3d(0, 0, 0) scale(1);
    }
    50% {
        transform: translate3d(2rem, -1.5rem, 0) scale(1.08);
    }
}

@media (prefers-reduced-motion: reduce) {
    .review-blob {
        animation: none;
    }
}
</style>

<script setup lang="ts">
import { Star } from '@lucide/vue';
import type { AcceptableValue } from 'reka-ui';
import { RadioGroupItem, RadioGroupRoot } from 'reka-ui';
import { computed, nextTick, ref } from 'vue';
import { cn } from '@/lib/utils';

const props = withDefaults(
    defineProps<{
        modelValue?: number | null;
        error?: string;
        disabled?: boolean;
    }>(),
    {
        modelValue: null,
        error: undefined,
        disabled: false,
    },
);

const emit = defineEmits<{
    'update:modelValue': [value: number];
}>();

const errorRef = ref<HTMLElement | null>(null);
const hoveredValue = ref<number | null>(null);

const activeValue = computed(() => hoveredValue.value ?? props.modelValue ?? 0);

const ratings = [
    { value: 1, label: 'Not at all likely' },
    { value: 2, label: 'Unlikely' },
    { value: 3, label: 'Neutral' },
    { value: 4, label: 'Likely' },
    { value: 5, label: 'Extremely likely' },
] as const;

function updateModelValue(value: AcceptableValue): void {
    emit('update:modelValue', Number(value));
}

async function focusError(): Promise<void> {
    await nextTick();
    errorRef.value?.focus();
}

defineExpose({ focusError });
</script>

<template>
    <div>
        <RadioGroupRoot
            :model-value="modelValue"
            :disabled="disabled"
            required
            aria-labelledby="rating-question"
            :aria-invalid="error ? 'true' : undefined"
            :aria-describedby="error ? 'score-error' : undefined"
            class="grid grid-cols-5 gap-2 sm:gap-3"
            @update:model-value="updateModelValue"
            @mouseleave="hoveredValue = null"
        >
            <RadioGroupItem
                v-for="rating in ratings"
                :key="rating.value"
                :value="rating.value"
                :aria-label="`${rating.value} — ${rating.label}`"
                :class="
                    cn(
                        'grid size-12 place-items-center rounded-2xl border border-input bg-background/60 shadow-xs backdrop-blur-sm sm:size-14',
                        'transition-all duration-200 outline-none',
                        'hover:scale-105 hover:border-primary/40 hover:bg-accent active:scale-95',
                        'focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/50',
                        'data-[state=checked]:scale-105 data-[state=checked]:border-transparent data-[state=checked]:bg-linear-to-br data-[state=checked]:from-(--review-accent-a) data-[state=checked]:to-(--review-accent-b) data-[state=checked]:shadow-(--review-accent-glow)',
                        'disabled:pointer-events-none disabled:opacity-50',
                    )
                "
                @mouseenter="hoveredValue = rating.value"
            >
                <Star
                    aria-hidden="true"
                    :class="[
                        'size-6 sm:size-7',
                        rating.value <= activeValue
                            ? modelValue === rating.value
                                ? 'fill-current text-white'
                                : 'fill-current text-(--review-accent-a)'
                            : 'text-foreground/70',
                    ]"
                />
            </RadioGroupItem>
        </RadioGroupRoot>

        <p
            v-if="error"
            id="score-error"
            ref="errorRef"
            tabindex="-1"
            role="alert"
            class="mt-2 text-sm font-medium text-red-600 dark:text-red-500"
        >
            {{ error }}
        </p>

        <div
            class="mt-3 flex items-center justify-between text-xs text-muted-foreground"
        >
            <span>Not at all likely</span>
            <span>Extremely likely</span>
        </div>
    </div>
</template>

<script setup>
import { X, AlertTriangle, Trash2 } from 'lucide-vue-next';

const props = defineProps({
    show: {
        type: Boolean,
        default: false
    },
    title: {
        type: String,
        default: 'Bevestigen'
    },
    message: {
        type: String,
        required: true
    },
    confirmText: {
        type: String,
        default: 'Bevestigen'
    },
    cancelText: {
        type: String,
        default: 'Annuleren'
    },
    type: {
        type: String,
        default: 'danger', // 'danger' | 'warning' | 'info'
        validator: (value) => ['danger', 'warning', 'info'].includes(value)
    }
});

const emit = defineEmits(['confirm', 'cancel']);

function confirm() {
    emit('confirm');
}

function cancel() {
    emit('cancel');
}

// Type-based styling
const typeStyles = {
    danger: {
        icon: 'bg-red-100 text-red-600',
        button: 'bg-red-100 text-red-700 hover:bg-red-200'
    },
    warning: {
        icon: 'bg-yellow-100 text-yellow-600',
        button: 'bg-yellow-100 text-yellow-700 hover:bg-yellow-200'
    },
    info: {
        icon: 'bg-blue-100 text-blue-600',
        button: 'bg-blue-100 text-blue-700 hover:bg-blue-200'
    }
};
</script>

<template>
    <Transition
        enter-active-class="transition ease-out duration-200"
        enter-from-class="opacity-0"
        enter-to-class="opacity-100"
        leave-active-class="transition ease-in duration-150"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
    >
        <div
            v-if="show"
            class="fixed inset-0 z-50 overflow-y-auto"
            @click="cancel"
        >
            <!-- Backdrop -->
            <div class="fixed inset-0 bg-black bg-opacity-50"></div>

            <!-- Modal -->
            <div class="flex min-h-full items-center justify-center p-4">
                <Transition
                    enter-active-class="transition ease-out duration-200"
                    enter-from-class="opacity-0 scale-95"
                    enter-to-class="opacity-100 scale-100"
                    leave-active-class="transition ease-in duration-150"
                    leave-from-class="opacity-100 scale-100"
                    leave-to-class="opacity-0 scale-95"
                >
                    <div
                        v-if="show"
                        class="relative bg-white rounded-lg shadow-sm max-w-md w-full"
                        @click.stop
                    >
                        <!-- Header -->
                        <div class="flex items-center justify-between p-6 border-b border-gray-200">
                            <div class="flex items-center gap-3">
                                <div :class="['w-10 h-10 rounded-full flex items-center justify-center', typeStyles[type].icon]">
                                    <AlertTriangle :size="20" />
                                </div>
                                <h3 class="text-xl font-semibold text-gray-900">{{ title }}</h3>
                            </div>
                            <button
                                @click="cancel"
                                class="text-gray-400 hover:text-gray-600 transition rounded-md p-1 hover:bg-gray-100"
                            >
                                <X :size="20" />
                            </button>
                        </div>

                        <!-- Body -->
                        <div class="p-6">
                            <p class="text-gray-700 text-sm">
                                <slot name="message">{{ message }}</slot>
                            </p>
                        </div>

                        <!-- Footer -->
                        <div class="flex gap-2 justify-end px-6 pb-6">
                            <button
                                @click="confirm"
                                :class="['px-3 py-1.5 rounded-md font-medium inline-flex items-center gap-1', typeStyles[type].button]"
                            >
                                <Trash2 :size="16" />
                                {{ confirmText }}
                            </button>
                            <button
                                @click="cancel"
                                class="px-3 py-1.5 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 font-medium inline-flex items-center gap-1"
                            >
                                <X :size="16" />
                                {{ cancelText }}
                            </button>
                        </div>
                    </div>
                </Transition>
            </div>
        </div>
    </Transition>
</template>

<script setup>
import Container from '@/Components/Container.vue';
import WhiteCard from '@/Components/WhiteCard.vue';
import BarChart from './BarChart.vue';
import CategoryMenu from './CategoryMenu.vue';
import { CurrencyDollarIcon, TagIcon } from '@heroicons/vue/20/solid';
import { ref } from 'vue';

const props = defineProps({
    label: {
        type: String,
        default: '',
    },
    categories: {
        type: Array,
        default: [],
    },
});

const selectedCategory = ref(props.categories[0]);
</script>

<template>
    <Container class="py-12 hidden md:block">
        <WhiteCard>
            <BarChart :label="label" :categories="categories" />
        </WhiteCard>
    </Container>

    <Container class="py-12">
        <WhiteCard class="flex-1">
            <div class="flex items-baseline justify-between mb-10">
                <div>
                    <h3 class="text-xl font-semibold leading-6 text-gray-900">Category Details</h3>
                    <p class="mt-2 text-sm text-gray-700">Select a category to see underlying expenses.</p>
                </div>

                <CategoryMenu :categories="categories" v-model="selectedCategory" />
            </div>

            <table class="min-w-full divide-y divide-gray-300 bg-white" v-if="selectedCategory !== null">
                <thead class="hidden md:table-header-group">
                    <tr>
                        <th scope="col" class="py-4 text-left text-sm font-semibold text-gray-900">Payee</th>
                        <th scope="col" class="py-4 text-left text-sm font-semibold text-gray-900 lg:w-40">Amount</th>
                        <th scope="col" class="py-4 text-left text-sm font-semibold text-gray-900 lg:w-40">Date</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-200">
                    <tr v-for="expense in selectedCategory.expenses" :key="expense.id" class="hover:bg-gray-100">
                        <td class="whitespace-nowrap py-3 text-md text-gray-500">
                            <div class="flex items-center gap-2">
                                <span
                                    :class="{ 'underline decoration-dotted underline-offset-2': expense.notes }"
                                    :title="expense.notes">
                                    {{ expense.payee }}
                                </span>
                                <TagIcon v-if="expense.has_receipt" class="h-3 w-3 text-gray-500" />
                                <CurrencyDollarIcon v-if="expense.is_business_expense" class="h-3 w-3 text-green-700" />
                            </div>
                            <div class="flex items-center gap-1 text-sm">
                                <span
                                    class="rounded-full block w-2 h-2"
                                    :style="{ backgroundColor: expense.category.color }">
                                </span>
                                {{ expense.category.name }}
                            </div>
                        </td>
                        <td class="whitespace-nowrap py-3 text-sm text-gray-500">
                            <span
                                :title="
                                    expense.has_fees
                                        ? 'Foreign Currency Conversion Fee: ' +
                                          expense.foreign_currency_conversion_fee_pretty
                                        : ''
                                "
                                :class="{
                                    'underline underline-offset-2 decoration-dotted cursor-pointer': expense.has_fees,
                                }"
                                class="font-bold text-md md:text-sm md:font-normal">
                                {{ expense.amount_pretty }}
                            </span>

                            <div class="md:hidden">
                                {{ expense.effective_date_pretty }}
                            </div>
                        </td>
                        <td class="hidden md:table-cell whitespace-nowrap py-3 text-sm text-gray-500">
                            {{ expense.effective_date_pretty }}
                        </td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td class="font-bold pt-10">Total</td>
                        <td class="font-bold pt-10">{{ selectedCategory.total }}</td>
                    </tr>
                </tfoot>
            </table>
        </WhiteCard>
    </Container>
</template>

<template>
    <app-layout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div
                    class="flex flex-col items-center mb-4"
                    v-if="$page.props.hasRole.key != 'member'"
                >
                    <div>
                        <div>Team Green</div>
                        <div
                            v-for="player in squadGreen"
                            :key="player.id"
                            class="my-2"
                        >
                            {{ player.name }}
                        </div>
                    </div>
                    <div class="mt-10">
                        <div>Team Orange</div>
                        <div
                            v-for="player in squadOrange"
                            :key="player.id"
                            class="my-2"
                        >
                            {{ player.name }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";

export default {
    components: { AppLayout },
    props: {
        squadGreen: Array,
        squadOrange: Array
    }
};
</script>

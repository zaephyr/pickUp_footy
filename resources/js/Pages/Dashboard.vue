<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{
                    $page.props.user.current_team_id
                        ? $page.props.user.current_team.name
                        : ""
                }}
                Dashboard
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div
                    class="bg-white overflow-hidden shadow-xl sm:rounded-lg flex flex-col items-center"
                >
                    <div
                        v-for="(member, index) in members"
                        :key="index"
                        class="justify-self-center"
                        :class="{
                            'text-green-500': member.attend == 1,
                            'text-red-400': member.attend == -1,
                            'mb-4':
                                index ==
                                members.map(el => el.attend).lastIndexOf(1),
                            'mt-4':
                                index ==
                                members.findIndex(el => el.attend == -1)
                        }"
                    >
                        {{ member.name }}
                    </div>
                    <div class="mt-4 mb-10 flex justify-around w-4/5 sm:w-1/2">
                        <button
                            class="bg-green-500 inline-flex items-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray transition ease-in-out duration-150"
                            @click="updateAttending(1)"
                        >
                            Pridem
                        </button>
                        <button
                            class="bg-red-400 inline-flex items-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray transition ease-in-out duration-150"
                            @click="updateAttending(-1)"
                        >
                            Ne pridem
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import Welcome from "@/Jetstream/Welcome";

export default {
    components: {
        AppLayout,
        Welcome
    },
    props: { members: Array, currentTeamMember: Object },
    data() {
        return {};
    },
    created() {
        this.cleanData();
    },

    methods: {
        cleanData() {
            this.members.sort((a, b) => b.attend - a.attend);
        },
        updateAttending(val) {
            if (this.currentTeamMember.attend != val) {
                this.$inertia.post(
                    "/dashboard/",
                    {
                        attend: val
                    },
                    {
                        onSuccess: () => {
                            this.cleanData();
                        },
                        onError: errors => {
                            console.log(errors);
                        }
                    }
                );
            }
        }
    }
};
</script>

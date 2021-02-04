<template>
    <app-layout>
        <template #header class="sm:pl-20">
            <span class="text-sm font-thin text-gray-400 ">Next match:</span>
            <h2 class="font-semibold text-xl leading-tight text-gray-600">
                {{ matchData.date }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div
                    class="flex justify-center mb-4"
                    v-if="$page.props.hasRole.key != 'member'"
                >
                    <button
                        class="bg-green-500 inline-flex items-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray transition ease-in-out duration-150"
                        @click="applyAttends()"
                    >
                        Apply Attending
                    </button>
                </div>
                <div
                    class="bg-white overflow-hidden shadow-xl sm:rounded-lg flex flex-col items-center pt-6"
                >
                    <div
                        v-for="(member, index) in members"
                        :key="index"
                        :class="[
                            {
                                'mb-4':
                                    index ==
                                    members.map(el => el.attend).lastIndexOf(1),
                                'mt-4':
                                    index ==
                                    members.findIndex(el => el.attend == -1),
                                'w-3/4 sm:w-1/2  md:w-2/5 lg:w-1/4 flex justify-between':
                                    $page.props.hasRole.key != 'member'
                            }
                        ]"
                    >
                        <span
                            :class="{
                                ja: $page.props.hasRole.key == 'member',
                                'text-green-500': member.attend == 1,
                                'text-red-400': member.attend == -1
                            }"
                            >{{ member.name }}</span
                        >
                        <span
                            class="text-center"
                            v-if="$page.props.hasRole.key != 'member'"
                        >
                            <jet-checkbox
                                name="attend"
                                v-model="attending[index].attendBool"
                            />
                        </span>
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
import JetCheckbox from "@/Jetstream/Checkbox";

export default {
    components: {
        AppLayout,
        JetCheckbox
    },
    props: { members: Array, currentTeamMember: Object, matchData: Object },
    data() {
        return {
            attending: []
        };
    },
    created() {
        this.cleanData();
    },

    methods: {
        applyAttends() {
            let membersToUpdate = this.attending
                .filter(
                    el =>
                        ((el.attend == 0 || el.attend == -1) &&
                            el.attendBool) ||
                        (el.attend == 1 && !el.attendBool)
                )
                .map(el => {
                    let param = {
                        id: el.member_id,
                        attend: el.attendBool ? 1 : -1
                    };
                    return param;
                });
            console.log(membersToUpdate);
            this.$inertia.put(
                route("dashboard.update.mass"),
                {
                    membersToUpdate
                },
                {
                    preserveScroll: true,
                    onSuccess: () => {
                        this.cleanData();
                    },
                    onError: errors => {
                        console.log(errors);
                    }
                }
            );
        },
        cleanData() {
            this.members.sort((a, b) => b.attend - a.attend);
            this.attending = this.members.map(el => {
                let member = {
                    member_id: el.member_id,
                    attend: el.attend,
                    attendBool: el.attend == 1
                };
                return member;
            });
        },
        updateAttending(val) {
            if (this.currentTeamMember.attend != val) {
                this.$inertia.put(
                    route("dashboard.update", this.currentTeamMember.id),
                    {
                        attend: val
                    },
                    {
                        preserveScroll: true,
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

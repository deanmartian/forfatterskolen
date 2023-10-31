<template>
    <div class="group-assignment-wrapper">
        <div class="col-lg-8">
            <template v-if="!isDataLoading">
                <div class="group-learner-list-wrapper">
                    <div class="title-container">
                        <h1>
                            {{ currentGroup.title }}
                        </h1>
                        <span v-if="currentGroup.assignment">
                            {{ currentGroup.assignment.title }}
                        </span>
                    </div>

                    <p class="date">
                        {{ trans('site.learner.deadline') }}
                        {{ currentGroup.submission_date_time_text }}
                    </p>

                    <div class="row">
                        <div class="col-md-6" v-for="groupLearner in groupLearnerList" :key="'learner-details ' + groupLearner.id">
                            <div class="learner-details-wrapper" :class="{'active' : currentUser.id === groupLearner.user_id}">
                                <div class="header">
                                    <img :src="groupLearner.user.profile_image">
                                    <h2 class="text-center">
                                        {{  currentUser.id === groupLearner.user_id ? trans('site.learner.you-text') 
                                            : trans('site.learner.learner-text') + " " + groupLearner.user_id }}
                                    </h2>
                                </div>
                                <div class="body">
                                    <template v-if="groupLearner.learnerManuscript.filename">
                                        <div class="file-container">
                                            <i class="fa fa-file-alt"></i>
                                            <b>
                                                {{ groupLearner.learnerManuscript.file_name }}
                                            </b>

                                            <a :href="groupLearner.learnerManuscript.file_link_url" class="btn blue-outline-btn" 
                                                v-if="currentUser.id === groupLearner.user_id">
                                                Forhåndsvisning
                                            </a>
                                            <a :href="groupLearner.learnerManuscript.filename" class="btn blue-outline-btn" 
                                            download v-else>
                                                {{ trans('site.learner.download-text') }}
                                                <i class="fa fa-download"></i>
                                            </a>
                                        </div>

                                        <p>
                                            <b>
                                                {{ groupLearner.learnerManuscript.assignment_type }}
                                            </b>
                                            - {{ groupLearner.learnerManuscript.where_in_script }}
                                        </p>
                                    </template>
                                    <em v-else>{{ trans('site.learner.no-manuscript-uploaded-text') }}</em>

                                    <template v-if="currentUser.id === groupLearner.user_id">
                                        <button type="button" class="btn red-global-btn disabled" 
                                            v-if="groupLearner.learnerManuscript.filename">
                                            {{ trans('site.learner.script-is-uploaded-text') }}
                                        </button>
                                    </template>
                                    <template v-else>

                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
            <template v-else>
                <div class="loading-wrapper">
                    <i class="fa fa-pulse fa-loading fa-spinner"></i>
                </div>
            </template>
        </div>
        <div class="col-lg-4">
            <div class="group-list-wrapper">
                <h2>
                    {{ trans('site.learner.groups') }}
                </h2>
                <div class="group-container" :class="{'active' : currentGroup.id === groupLearner.assignment_group_id}" 
                    v-for="groupLearner in learners" :key="groupLearner.id" 
                    @click="selectGroupLearner(groupLearner)">
                    <h3>
                        {{ groupLearner.group.title }}
                    </h3>
                    <b>
                        {{ trans('site.front.course-text') }}:
                        {{ groupLearner.group.assignment.course.title }}
                    </b>
                    <p>
                        {{ trans('site.learner.assignment-single') }}:
                        {{ groupLearner.group.assignment.title }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {

    props: ['learners', 'current-user'],

    data() {
        return {
            currentGroup: {},
            groupLearnerList: null,
            isDataLoading: false,
        }
    },

    methods: {
        selectGroupLearner(groupLearner) {
            this.isDataLoading = true;
            this.currentGroup = groupLearner.group;
            axios.get('/account/assignment/group/' + groupLearner.assignment_group_id + '/learner-details')
                .then(response => {
                    console.log(response.data.groupLearnerList);
                    this.groupLearnerList = response.data.groupLearnerList;
                    console.log(this.groupLearnerList);
                    this.isDataLoading = false;
                });
        }
    },

    mounted() {
        this.selectGroupLearner(this.learners[0]);
    }
}
</script>
<template>
    <div>
        <div class="panel">
            <div class="panel-header" style="padding: 10px">
                <em><b>Time Register</b></em>
            </div>
            <div class="panel-body">
                <button class="btn btn-success btn-sm pull-right" @click="showTimeFormModal()">
                    + Add Time Register
                </button>
                <div class="clearfix"></div>
                <div class="table-users">
                    <table class="table table-responsive">
                        <thead>
                        <tr>
                            <th>Date</th>
                            <th>Number of hours</th>
                            <th>Notes</th>
                            <th width="150"></th>
                        </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(projectTimeRegister, index) in projectTimeRegisters" :key="index">
                                <td>{{ projectTimeRegister.date }}</td>
                                <td>{{ projectTimeRegister.time }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    props: ['current-project','project-time-list'],

    data() {
        return {
            project: this.currentProject,
            projectTimeRegisters: this.projectTimeList,
        }
    },

    mounted() {
        console.log("currently not being used");
    }
}
</script>
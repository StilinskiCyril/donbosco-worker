<template>
    <div>
        <div class="row">
            <div class="col-xl-8 mx-auto">
                <div class="card border-top border-0 border-4 border-primary">
                    <div class="card-body p-5">
                        <h3>Create New Group</h3>
                        <form class="row g-3">
                            <div class="col-md-12">
                                <label for="name" class="form-label">Group Name</label>
                                <input type="text" class="form-control" v-model="createForm.name">
                            </div>
                            <button v-if="createForm.processing" class="btn btn-primary btn-rounded btn-block" type="button" disabled>
                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                Processing...
                            </button>
                            <button v-else v-on:click.prevent="createGroup()" type="button" class="btn btn-primary btn-block">Create Group</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xl-12">
                <div class="card border-top border-0 border-4 border-primary">
                    <div class="card-body p-5">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered" style="width:100%">
                                <thead>
                                <tr>
                                    <th>Group Name</th>
                                    <th>Sub-Groups</th>
                                    <th colspan="1">Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="group in pagination.data" :key="group.uuid">
                                    <td>{{ group.name }}</td>
                                    <td>{{ group.sub_groups_count }}</td>
                                    <td>
                                        <button @click.prevent="showUpdateGroupModal(group)" class="btn btn-primary btn-block"><i class="bx bx-edit"></i> Update Group</button>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer">
                        <ul class="pagination justify-content-center" @paginate="loadGroups()">
                            <li class="page-item" v-if="pagination.current_page > 1">
                                <a href="javascript:void(0)" class="page-link btn btn-rounded btn-inverse-dark" aria-label="Previous" v-on:click.prevent="changePage(pagination.current_page - 1)">
                                    <span aria-hidden="true">Previous</span>
                                </a>
                            </li>
                            <li class="page-item" v-for="page in pagesNumber" :class="{'active': page === pagination.current_page}">
                                <a class="page-link btn btn-rounded btn-inverse-dark" href="javascript:void(0)" v-on:click.prevent="changePage(page)">{{ page }}</a>
                            </li>
                            <li class="page-item" v-if="pagination.current_page < pagination.last_page">
                                <a href="javascript:void(0)" class="page-link btn btn-rounded btn-dark" aria-label="Next" v-on:click.prevent="changePage(pagination.current_page + 1)">
                                    <span aria-hidden="true">Next</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Update Group Modal -->
        <div class="modal fade" id="updateGroupModal" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg bg-light">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="card border-top border-0 border-4 border-primary">
                            <div class="card-body p-5">
                                <form class="row g-3">
                                    <div class="col-md-12">
                                        <label for="name" class="form-label">Name</label>
                                        <input type="text" class="form-control" v-model="updateForm.name">
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button v-if="updateForm.processing" class="btn btn-primary btn-rounded" type="button" disabled>
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            Processing...
                        </button>
                        <button v-else type="button" class="btn btn-primary" @click="updateGroup()">
                            Update Group
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import Swal from "sweetalert2";
    export default {
        name: "GroupsComponent",
        data() {
            return {
                createForm: {
                    name: undefined,
                    processing: false
                },
                updateForm: {
                    group_uuid: undefined,
                    name: undefined,
                    processing: false
                },
                pagination: {
                    total: 0,
                    per_page: 2,
                    from: 1,
                    to: 0,
                    current_page: 1,
                    data: [],
                    last_page: 0
                },
                offset: 10,
            }
        },
        mounted() {
            this.loadGroups();
        },
        methods: {
            loadGroups(){
                const payLoad = {
                    sort_by : 'latest'
                };
                this.createForm.processing = true;
                axios.post(`/load-groups?page=${this.pagination.current_page}`, payLoad).then(response => {
                    this.pagination = response.data;
                }).catch(error => {
                    //
                }).finally(() => {
                    this.createForm.processing = false;
                });
            },
            createGroup(){
                if (!this.createForm.name){
                    Swal.fire('Error!', 'Name is required', 'warning');
                    return;
                }
                const payLoad = {
                    name : this.createForm.name
                };
                this.createForm.processing = true;
                axios.post(`create-group`, payLoad).then(response => {
                    if (response.data.status){
                        Swal.fire('Success!', response.data.message, 'success');
                        this.createForm.name = undefined;
                        this.loadGroups();
                    } else {
                        Swal.fire('Error!', response.data.message, 'error');
                    }
                }).catch(error => {
                    if (error.response && error.response.status === 422) {
                        Swal.fire('Error!', JSON.stringify(error.response.data.errors), 'error');
                    } else {
                        Swal.fire('Error!', 'Something went wrong. Please try again.', 'error');
                    }
                }).finally(() => {
                    this.createForm.processing = false;
                });
            },
            showUpdateGroupModal(group){
                this.updateForm.group_uuid = group.uuid;
                this.updateForm.name = group.name;
                $('#updateGroupModal').modal('show');
            },
            updateGroup(){
                if (!this.updateForm.name){
                    Swal.fire('Error!', 'Name is required', 'warning');
                    return;
                }
                const payLoad = {
                    name : this.updateForm.name
                };
                this.updateForm.processing = true;
                axios.post(`update-group/${this.updateForm.group_uuid}`, payLoad).then(response => {
                    if (response.data.status){
                        Swal.fire('Success!', response.data.message, 'success');
                        this.updateForm.name = undefined;
                        $('#updateGroupModal').modal('hide');
                        this.loadGroups();
                    } else {
                        Swal.fire('Error!', response.data.message, 'error');
                    }
                }).catch(error => {
                    if (error.response && error.response.status === 422) {
                        Swal.fire('Error!', JSON.stringify(error.response.data.errors), 'error');
                    } else {
                        Swal.fire('Error!', 'Something went wrong. Please try again.', 'error');
                    }
                }).finally(() => {
                    this.updateForm.processing = false;
                });
            },
            changePage(page) {
                this.pagination.current_page = page;
                this.loadGroups();
            },
        },
        computed: {
            pagesNumber() {
                if (!this.pagination.to) {
                    return [];
                }
                let from = this.pagination.current_page - this.offset;
                if (from < 1) {
                    from = 1;
                }
                let to = from + (this.offset * 2);
                if (to >= this.pagination.last_page) {
                    to = this.pagination.last_page;
                }
                let pagesArray = [];
                for (let page = from; page <= to; page++) {
                    pagesArray.push(page);
                }
                return pagesArray;
            },
        }
    }
</script>

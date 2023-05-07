<template>
    <div>
        <div class="row">
            <div class="col-xl-8 mx-auto">
                <div class="card border-top border-0 border-4 border-primary">
                    <div class="card-body p-5">
                        <h3>Create New Sub-Group</h3>
                        <form class="row g-3">
                            <div class="col-md-12">
                                <label for="project_uuid" class="form-label">Select Group</label>
                                <select class="form-control" v-model="createForm.group_uuid">
                                    <option v-for="group in groups.data" :value="group.uuid">{{ group.name }}</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="name" class="form-label">Sub-Group Name</label>
                                <input type="text" class="form-control" v-model="createForm.name">
                            </div>
                            <div class="col-md-6">
                                <label for="ministry" class="form-label">Ministry</label>
                                <input type="text" class="form-control" v-model="createForm.ministry">
                            </div>
                            <button v-if="createForm.processing" class="btn btn-primary btn-rounded btn-block" type="button" disabled>
                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                Processing...
                            </button>
                            <button v-else v-on:click.prevent="createSubGroup()" type="button" class="btn btn-primary btn-block">Create Sub-Group</button>
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
                                    <th>Sub-Group Name</th>
                                    <th>Ministry</th>
                                    <th colspan="1">Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="sub_group in pagination.data" :key="sub_group.uuid">
                                    <td>{{ sub_group.group.name }}</td>
                                    <td>{{ sub_group.name }}</td>
                                    <td>{{ sub_group.ministry }}</td>
                                    <td>
                                        <button @click.prevent="showUpdateSubGroupModal(sub_group)" class="btn btn-primary btn-block"><i class="bx bx-edit"></i> Update Sub-Group</button>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer">
                        <ul class="pagination justify-content-center" @paginate="loadSubGroups()">
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

        <!-- Update Sub-Group Modal -->
        <div class="modal fade" id="updateSubGroupModal" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg bg-light">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="card border-top border-0 border-4 border-primary">
                            <div class="card-body p-5">
                                <form class="row g-3">
                                    <div class="col-md-12">
                                        <label for="group_uuid" class="form-label">Select Group</label>
                                        <select class="form-control" v-model="updateForm.group_uuid">
                                            <option v-for="group in groups.data" :value="group.uuid">{{ group.name }}</option>
                                        </select>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="name" class="form-label">Name</label>
                                        <input type="text" class="form-control" v-model="updateForm.name">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="msisdn" class="form-label">Msisdn/Phone</label>
                                        <input type="text" class="form-control" v-model="updateForm.ministry">
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
                        <button v-else type="button" class="btn btn-primary" @click="updateSubGroup()">
                            Update Sub-Group
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
        name: "SubGroupsComponent",
        data() {
            return {
                groups: {},
                createForm: {
                    group_uuid: undefined,
                    name: undefined,
                    ministry: undefined,
                    processing: false
                },
                updateForm: {
                    group_uuid: undefined,
                    sub_group_uuid: undefined,
                    name: undefined,
                    ministry: undefined,
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
            this.loadSubGroups();
        },
        methods: {
            loadGroups(){
                const payLoad = {
                    sort_by : 'latest'
                };
                this.createForm.processing = true;
                axios.post(`/load-groups?page=${this.pagination.current_page}`, payLoad).then(response => {
                    this.groups = response.data;
                }).catch(error => {
                    //
                }).finally(() => {
                    this.createForm.processing = false;
                });
            },
            loadSubGroups(){
                const payLoad = {
                    sort_by : 'latest'
                };
                this.createForm.processing = true;
                axios.post(`/load-sub-groups?page=${this.pagination.current_page}`, payLoad).then(response => {
                    this.pagination = response.data;
                }).catch(error => {
                    //
                }).finally(() => {
                    this.createForm.processing = false;
                });
            },
            createSubGroup(){
                if (!this.createForm.group_uuid){
                    Swal.fire('Error!', 'Group is required', 'warning');
                    return;
                }
                if (!this.createForm.name){
                    Swal.fire('Error!', 'Name is required', 'warning');
                    return;
                }
                if (!this.createForm.ministry){
                    Swal.fire('Error!', 'Ministry is required', 'warning');
                    return;
                }
                const payLoad = {
                    name : this.createForm.name,
                    ministry : this.createForm.ministry
                };
                this.createForm.processing = true;
                axios.post(`create-sub-group/${this.createForm.group_uuid}`, payLoad).then(response => {
                    if (response.data.status){
                        Swal.fire('Success!', response.data.message, 'success');
                        this.createForm.group_uuid = undefined;
                        this.createForm.name = undefined;
                        this.createForm.ministry = undefined;
                        this.loadGroups();
                        this.loadSubGroups();
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
            showUpdateSubGroupModal(sub_group){
                this.updateForm.group_uuid = sub_group.group.uuid;
                this.updateForm.sub_group_uuid = sub_group.uuid;
                this.updateForm.name = sub_group.name;
                this.updateForm.ministry = sub_group.ministry;
                $('#updateSubGroupModal').modal('show');
            },
            updateSubGroup(){
                if (!this.updateForm.group_uuid){
                    Swal.fire('Error!', 'Group is required', 'warning');
                    return;
                }
                if (!this.updateForm.name){
                    Swal.fire('Error!', 'Name is required', 'warning');
                    return;
                }
                if (!this.updateForm.ministry){
                    Swal.fire('Error!', 'Ministry is required', 'warning');
                    return;
                }
                const payLoad = {
                    name : this.updateForm.name,
                    ministry : this.updateForm.ministry
                };
                this.updateForm.processing = true;
                axios.post(`update-sub-group/${this.updateForm.group_uuid}/${this.updateForm.sub_group_uuid}`, payLoad).then(response => {
                    if (response.data.status){
                        Swal.fire('Success!', response.data.message, 'success');
                        this.updateForm.group_uuid = undefined;
                        this.updateForm.name = undefined;
                        this.updateForm.ministry = undefined;
                        $('#updateSubGroupModal').modal('hide');
                        this.loadGroups();
                        this.loadSubGroups();
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
                this.loadSubGroups();
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

<template>
    <div class="container">
        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-header">Table to CSV Generator</div>

                    <div class="card-body table-responsive">
                        <table class="table table-bordered ">
                            <thead>
                            <tr>
                                <th></th>
                                <th v-for="column in columns">
                                    <input type="text"
                                           class="form-control"
                                           ref="columns"
                                           :value="column.key"
                                           @input="updateColumnKey(column, $event)"
                                    />
                                </th>
                            </tr>
                            </thead>
                            <tbody>

                            <tr v-for="(row, index) in data">
                                <td>
                                    <b-button variant="link" @click="remove_row(index)"><b-icon icon="trash-fill" variant="danger"></b-icon></b-button>
                                </td>
                                <td v-for="column in columns">
                                    <input type="text" class="form-control" v-model="row[column.key]"/>
                                </td>
                            </tr>
                            </tbody>
                        </table>

                        <div class="p-3">
                            <button type="button" class="btn btn-secondary" @click="add_column">Add Column</button>
                            <button type="button" class="btn btn-secondary" @click="add_row">Add Row</button>
                        </div>
                    </div>

                    <div class="card-footer text-right">
                        <b-button class="btn" variant="outline-primary" type="button" :disabled="!data.length" @click="submit()">Export</b-button>
                    </div>
                </div>
            </div>
        </div>
    </div>

</template>

<script>
export default {
    name: "CSVGenerator",

    data() {
        return {
            data: [
                {
                    first_name: 'John',
                    last_name: 'Doe',
                    emailAddress: 'john.doe@example.com'
                },
                {
                    first_name: 'John',
                    last_name: 'Doe',
                    emailAddress: 'john.doe@example.com'
                },

            ],
            columns: [
                {key: 'first_name'},
                {key: 'last_name'},
                {key: 'emailAddress'},

            ]
        }
    },

    methods: {
        add_row() {
            // Add new row to data with column keys
            this.data.push(this.columns.reduce((obj, item) => {
                return {
                    ...obj,
                    [item['key']]: '',
                };
            }, {}))
        },

        remove_row(row_index) {
            // remove the given row
            this.data.splice(row_index, 1);
        },

        add_column() {
            if(!this.columns.find(column => column.key === '')) this.columns.push({key: ''})
        },

        updateColumnKey(column, event) {
            let oldKey = column.key;

            let columnKeyExists = !!this.columns.find(column => column.key === event.target.value);

            column.key = event.target.value;

            if (columnKeyExists) {
                column.key = event.target.value.substring(0, event.target.value.length - 1);
                return;
            }

            this.data.forEach(
                (row) => {
                    if (row.hasOwnProperty(oldKey)) {
                        row[column.key] = row[oldKey];
                        delete row[oldKey];
                    } else {
                        row[column.key] = '';
                    }
                }
            )
        },

        submit() {
            return axios.patch('/api/csv-export', this.data).then(response => {
                if (response.data) {
                    let blob = new Blob([response.data], {type: 'text/csv'});
                    let link = document.createElement('a');
                    link.href = window.URL.createObjectURL(blob);
                    // TODO make random name
                    link.download = 'TestFileName.csv';
                    link.click();
                }
            });
        }
    },

    watch: {
    }
}
</script>

<style scoped>

</style>

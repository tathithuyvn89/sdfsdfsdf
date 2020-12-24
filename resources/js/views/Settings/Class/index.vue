<template>
  <div class="app-container">
    <div v-if="hidden === true" class="filter-container">
      <el-button type="success" @click="Create()">{{
        $t('button.create')
      }}</el-button>
      <el-button
        slot="reference"
        type="danger"
        @click="handlePopDeleteMany()"
      >{{ $t('button.delete') }}</el-button>

      <el-input
        v-model="listQuery.keyword"
        :placeholder="$t('placeholder.search')"
        class="inputSearch"
        @keyup.enter.native="handleFilter"
      />
    </div>

    <el-table
      :key="tableKey"
      ref="tableClass"
      v-loading="listLoading"
      :data="ClassList"
      :default-sort="{ prop: 'name' }"
      border
      fit
      style="width: 100%"
      class="tableClassList"
      @sort-change="sortTableChange"
      @selection-change="handleSelectionChange"
    >
      <el-table-column type="selection" width="55" align="center" />

      <el-table-column
        :label="$t('table.col_class_en')"
        align="center"
        sortable
        prop="name"
        min-width="200"
      >
        <template slot-scope="scope">
          <span>{{ scope.row.name_en }}</span>
        </template>
      </el-table-column>

      <el-table-column
        :label="$t('table.col_class_ja')"
        align="center"
        sortable
        prop="name"
        min-width="200"
      >
        <template slot-scope="scope">
          <span>{{ scope.row.name_ja }}</span>
        </template>
      </el-table-column>

      <el-table-column
        v-if="hidden === true"
        :label="$t('table.col_operations')"
        align="center"
        min-width="150"
      >
        <template slot-scope="{ row }">
          <el-button
            type="primary"
            icon="el-icon-edit"
            circle
            @click="handleEdit(row)"
          />

          <el-button
            slot="reference"
            icon="el-icon-delete"
            type="danger"
            circle
            @click="handlePopDeleteOne(row)"
          />
        </template>
      </el-table-column>
    </el-table>

    <el-pagination
      :current-page.sync="listQuery.page"
      :page-size="10"
      :total="total"
      background
      layout="total, prev, pager, next, jumper"
      :hide-on-single-page="true"
      class="pagination"
      @current-change="getList"
    />

    <el-dialog
      :title="titleCnE"
      :visible.sync="dialogVisible"
      @close="closeDialog('ruleForm')"
    >
      <el-form
        ref="ruleForm"
        :model="ruleForm"
        :rules="rules"
        class="ruleForm"
        label-width="200px"
      >
        <el-form-item
          :label="$t('popup.class.tClass_en')"
          prop="name_en"
          class="inputClassname"
        >
          <el-input
            v-model="ruleForm.name_en"
            :placeholder="$t('popup.class.pClass_en')"
          />
        </el-form-item>

        <el-form-item
          :label="$t('popup.class.tClass_ja')"
          prop="name_ja"
          class="inputClassname"
        >
          <el-input
            v-model="ruleForm.name_ja"
            :placeholder="$t('popup.class.pClass_ja')"
          />
        </el-form-item>
      </el-form>

      <div slot="footer" class="dialog-footer">
        <el-button @click="dialogVisible = false">
          {{ $t('button.close') }}
        </el-button>
        <el-button :disabled="isproces" type="primary" @click="submit()">
          {{ $t('button.save') }}
        </el-button>
      </div>
    </el-dialog>

    <el-dialog
      :title="$t('popup.class.delete.title')"
      :visible.sync="dialogVisibleDelete"
      center
      width="30%"
    >
      <center>{{ $t('popup.class.delete.content') }}</center>
      <div slot="footer" class="dialog-footer">
        <el-button @click="dialogVisibleDelete = false">
          {{ $t('button.close') }}
        </el-button>
        <el-button type="danger" @click="submitConfrimDelete()">
          {{ $t('button.delete') }}
        </el-button>
      </div>
    </el-dialog>
  </div>
</template>

<script>
import { fetchList, deleteClass, addClass, editClass } from '@/api/class';

export default {
  data() {
    return {
      currentNumberRow: 10, // Number of departments per page
      ClassList: [], // The array to contain all the departments on a page
      multipleSelection: [], // Array containing ids selected for deletion
      dialogVisible: false, // Toggle popup
      ruleForm: {
        // The object contains the department data when editing
        id: undefined,
        name_en: '',
        name_ja: '',
      },
      tempActions: '', // Check the event whether the user wants to Add or Edit
      tempActionsDelete: '', // Check the event whether the user wants to DeleteOne or DeleteMany
      rules: {
        // Rule validate
        name_en: [
          {
            required: true,
            message: this.$t('popup.class.rule.rname'),
            trigger: ['blur', 'change'],
          },
          {
            max: 255,
            message: this.$t('popup.class.rule.lname'),
            trigger: ['blur', 'change'],
          },
        ],
        name_ja: [
          {
            required: true,
            message: this.$t('popup.class.rule.rname'),
            trigger: ['blur', 'change'],
          },
          {
            max: 255,
            message: this.$t('popup.class.rule.lname'),
            trigger: ['blur', 'change'],
          },
        ],
      },
      tableKey: 0,
      total: 0, // Total of department
      last_page: undefined, // Last page
      lengthData: undefined, // Check the remaining data on the page
      listQuery: {
        // Query to call api
        page: 1,
        keyword: undefined,
      },
      listLoading: true, // Event loading table
      hidden: true,
      dialogVisibleDelete: false,
      temp: undefined,
      isproces: false,
      titleCnE: undefined,
      listIdDeleteFaild: [],
      listIndexDeleteFaild: [],
    };
  },
  computed: {
    listIdDeleteFaildChange() {
      return this.listIdDeleteFaild;
    },
  },
  watch: {
    listIdDeleteFaildChange() {
      this.listIndexDeleteFaild = this.getListIndexDeleteFaild();
      this.toggleTableRowClassName(this.$refs.tableClass.$el, this.listIndexDeleteFaild);
    },
  },
  created() {
    this.getList(); // Call page 1 when loaded page
  },
  methods: {
    toggleSelection(rows) {
      if (rows) {
        rows.forEach((row) => {
          this.$refs.multipleTable.toggleRowSelection(row);
        });
      } else {
        this.$refs.multipleTable.clearSelection();
      }
    },
    handleSelectionChange(val) {
      this.multipleSelection = [];
      for (var i = 0; i < val.length; i++) {
        this.multipleSelection.push(val[i].id);
      }
    },
    submitConfrimDelete() {
      this.$message.closeAll();
      if (this.tempActionsDelete === 'DeleteOne') {
        this.handleDelete(this.temp);
      }
      if (this.tempActionsDelete === 'DeleteMany') {
        this.DeleteMany();
      }
    },
    submit() {
      this.$refs['ruleForm'].validate((valid) => {
        if (valid) {
          this.isproces = true;
          if (this.tempActions === 'Create') {
            const resCreate = addClass({
              name_en: this.ruleForm.name_en,
              name_ja: this.ruleForm.name_ja,
            });

            resCreate.then((data) => {
              if (data.message === 'Record successfully') {
                this.isproces = false;
                this.dialogVisible = false;
                this.$message({
                  message: this.$t('popup.class.notify.createSuccess'),
                  type: 'success',
                });
                this.listLoading = true;
                this.ClassList = data.page_infor.data;
                this.last_page = data.page_infor.last_page;
                this.listQuery.page = data.page_infor.last_page;
                this.total = data.page_infor.total;
                this.lengthData = data.page_infor.data.length;
                this.listLoading = false;
              } else {
                this.isproces = false;
                this.dialogVisible = false;
                this.$message({
                  message: this.$t('popup.class.notify.createFailed'),
                  type: 'error',
                });
              }
            });
          }

          if (this.tempActions === 'Edit') {
            const resEdit = editClass({
              id: this.ruleForm.id,
              name_en: this.ruleForm.name_en,
              name_ja: this.ruleForm.name_ja,
              current_page: this.listQuery.page,
            });
            resEdit.then((data) => {
              if (data.message === 'Edit successfully') {
                this.isproces = false;
                this.dialogVisible = false;
                this.$message({
                  message: this.$t('popup.class.notify.editSuccess'),
                  type: 'success',
                });
                this.listLoading = true;
                this.ClassList = data.page_infor.data;
                this.last_page = data.page_infor.last_page;
                this.listQuery.page = data.page_infor.current_page;
                this.lengthData = data.page_infor.data.length;
                this.total = data.page_infor.total;
                this.listQuery.keyword = '';
                this.listLoading = false;
              } else {
                this.isproces = false;
                this.dialogVisible = false;
                this.$message({
                  message: this.$t('popup.class.notify.editFailed'),
                  type: 'error',
                });
              }
            });
          }
        }
      });
    },
    // Delete One
    async handleDelete(row) {
      this.sortTableChange();
      // Function Delete one
      const classs = Object.assign({}, row);
      var current_page = this.listQuery.page;
      if (this.lengthData > 1) {
        current_page = this.listQuery.page;
      } else if (this.lengthData === 1) {
        current_page = this.listQuery.page - 1;
      }

      await deleteClass({
        id_list: [classs.id],
        current_page: current_page,
      })
        .then((res) => {
          this.$message({
            message: this.$t('popup.class.notify.deleteSuccess'),
            type: 'success',
          });
          this.listLoading = true;
          this.ClassList = res.page_infor.data;
          this.total = res.page_infor.total;
          this.listQuery.last_page = res.page_infor.last_page;
          this.listQuery.page = res.page_infor.current_page;
          this.lengthData = res.page_infor.data.length;
          this.listLoading = false;
          this.dialogVisibleDelete = false;
        })
        .catch((err) => {
          this.dialogVisibleDelete = false;
          if (err.response.status === 500 && err.response.data.error === 'errorPage.errorClassDelete') {
            this.listIdDeleteFaild = err.response.data.delete_faild;
          }
        });
    },
    // Delete Many
    async DeleteMany() {
      this.sortTableChange();
      var dataDelete = Object.values(this.multipleSelection);
      if (this.multipleSelection.length === 0) {
        this.$message({
          message: 'Please choose Class want to delete',
          type: 'warning',
        });
      } else if (this.multipleSelection.length > 0) {
        var current_page = this.listQuery.page;
        this.lengthData -= this.multipleSelection.length;
        if (this.lengthData > 1) {
          current_page = this.listQuery.page;
        }
        if (this.lengthData === 1 || this.lengthData === 0) {
          if (this.listQuery.page === this.last_page) {
            current_page = this.listQuery.page - 1;
            this.listQuery.page -= 1;
          }
        }

        await deleteClass({
          id_list: dataDelete,
          current_page: current_page,
        })
          .then((res) => {
            this.$message({
              message: this.$t('popup.class.notify.deleteSuccess'),
              type: 'success',
            });

            this.listLoading = true;
            this.ClassList = res.page_infor.data;
            this.total = res.page_infor.total;
            this.last_page = res.page_infor.last_page;
            this.listQuery.page = res.page_infor.current_page;
            this.lengthData = res.page_infor.data.length;
            this.listQuery.keyword = '';
            this.listLoading = false;
            this.dialogVisibleDelete = false;
            this.listIdDeleteFaild = res.delete_faild;
          })
          .catch((err) => {
            this.dialogVisibleDelete = false;
            if (err.response.status === 500 && err.response.data.error === 'errorPage.errorClassDelete') {
              this.listIdDeleteFaild = err.response.data.delete_faild;
            }
          });
      }
    },
    getListIndexDeleteFaild() {
      var listIndex = [];

      for (var i = 0; i < this.ClassList.length; i++) {
        for (var j = 0; j < this.listIdDeleteFaild.length; j++) {
          if (this.ClassList[i].id === this.listIdDeleteFaild[j].id) {
            listIndex.push(i);
          }
        }
      }

      return listIndex;
    },
    // Toggle Show Highlight when Delete Faild
    toggleTableRowClassName(selectEl, listIndex) {
      // Add highlight
      this.addTableRowClassName(selectEl, listIndex);
      // Remove highlight
      this.removeTableRowClassName(selectEl);
    },
    addTableRowClassName(selectEl, listIndex) {
      var els = selectEl.querySelectorAll(
        '.el-table__body-wrapper > table > tbody > tr'
      );
      // Loop to delete class "danger-row" in el
      for (var el = 0; el < els.length; el++) {
        // Check el has class "danger-row" ==> Pass to delete class "danger-row"
        if (!els[el].classList.contains('danger-row')) {
          for (var index = 0; index < listIndex.length; index++) {
            if (el === listIndex[index]) {
              els[el].classList.add('danger-row');
            }
          }
        }
      }
    },
    removeTableRowClassName(selectEl) {
      // Set Time Out
      setTimeout(function(){
        // Get list row of table
        var els = selectEl.querySelectorAll(
          '.el-table__body-wrapper > table > tbody > tr'
        );
        // Loop to delete class "danger-row" in el
        for (var el = 0; el < els.length; el++) {
          // Check el has class "danger-row" ==> Pass to delete class "danger-roww"
          if (els[el].classList.contains('danger-row')) {
            els[el].classList.remove('danger-row');
          }
        }
      }, 4000); // Time out 4s
    },
    // Remove highlight when user sort table
    sortTableChange() {
      var selectEl = this.$refs.tableClass.$el;
      var els = selectEl.querySelectorAll(
        '.el-table__body-wrapper > table > tbody > tr'
      );
      // Loop to delete class "danger-row" in el
      for (var el = 0; el < els.length; el++) {
        // Check el has class "danger-row" ==> Pass to delete class "danger-roww"
        if (els[el].classList.contains('danger-row')) {
          els[el].classList.remove('danger-row');
        }
      }
    },
    Create() {
      // Event click buttom create
      this.dialogVisible = true;
      this.tempActions = 'Create';
      this.titleCnE = this.$t('popup.class.tCreate');
    },
    handleEdit(row) {
      // Event click buttom edit
      this.dialogVisible = true;
      this.tempActions = 'Edit';
      this.titleCnE = this.$t('popup.class.tEdit');
      this.ruleForm = Object.assign({}, row);
    },
    handlePopDeleteMany() {
      this.dialogVisibleDelete = true;
      this.tempActionsDelete = 'DeleteMany';
    },
    handlePopDeleteOne(row) {
      this.dialogVisibleDelete = true;
      this.tempActionsDelete = 'DeleteOne';
      this.temp = row;
    },
    submitForm(formName) {
      // Validate form
      this.$refs[formName].validate((valid) => {
        if (valid) {
          return true;
        } else {
          return false;
        }
      });
    },
    closeDialog(formName) {
      // Event popup close
      this.$refs[formName].resetFields();
      this.ruleForm.name_en = '';
      this.ruleForm.name_ja = '';
    },
    async getList() {
      // Get data in page
      this.listLoading = true;
      const data = await fetchList(this.listQuery);
      this.ClassList = data.data;
      this.total = data.meta.total;
      this.last_page = data.meta.last_page;
      this.lengthData = data.data.length;
      this.listLoading = false;
    },
    handleFilter() {
      // Get date when search
      this.listQuery.page = 1;
      this.getList();
    },
  },
};
</script>

<style>
.el-table .danger-row {
  background: rgb(253, 226, 226);
  color: #F56C6C;
  transition-duration: 1s;
}
</style>

<style scoped>
.inputSearch {
  margin-left: 10px;
  width: 200px;
}

.inputClassname {
  margin-top: 30px;
}

.btnForm {
  text-align: right;
}

.pagination {
  margin-top: 10px;
  text-align: right;
}
.center {
  text-align: center;
}
</style>

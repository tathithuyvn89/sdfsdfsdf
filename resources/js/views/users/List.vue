<template>
  <div class="app-container">
    <!-- top tags -->
    <div class="filter-container">
      <el-input
        v-model="query.keyword"
        :placeholder="$t('table.keyword')"
        style="width: 200px"
        class="filter-item"
        @keyup.enter.native="handleFilter"
      />
      <!-- <el-select
        v-model="query.role"
        :placeholder="$t('table.role')"
        clearable
        style="width: 90px"
        class="filter-item"
        @change="handleFilter"
      >
        <el-option
          v-for="(item, index) in optionsRole"
          :key="index"
          :label="item.label | uppercaseFirst"
          :value="item.value"
        />
      </el-select> -->
      <el-button
        v-waves
        class="filter-item"
        type="primary"
        icon="el-icon-search"
        @click="handleFilter"
      >
        {{ $t('table.search') }}
      </el-button>
      <el-button
        class="filter-item"
        style="margin-left: 10px"
        type="primary"
        icon="el-icon-plus"
        @click="handleCreate"
      >
        {{ $t('table.add') }}
      </el-button>
      <!-- <el-button
        v-waves
        :loading="downloading"
        class="filter-item"
        type="primary"
        icon="el-icon-download"
        @click="handleDownload"
      >
        {{ $t('table.export') }}
      </el-button> -->
    </div>
    <!-- User table -->
    <el-table
      v-loading="loading"
      :data="list"
      border
      fit
      highlight-current-row
      style="width: 100%"
    >
      <el-table-column align="center" :label="$t('profile.name')">
        <template slot-scope="scope">
          <span>{{ scope.row.name }}</span>
        </template>
      </el-table-column>

      <el-table-column align="center" :label="$t('profile.email')">
        <template slot-scope="scope">
          <span>{{ scope.row.email }}</span>
        </template>
      </el-table-column>

      <el-table-column align="center" :label="$t('profile.edit')" width="150">
        <template slot-scope="{ row }">
          <el-button
            v-permission="['manage user']"
            type="primary"
            size="small"
            icon="el-icon-edit"
            @click="openPop(row)"
          >
            {{ $t('profile.edit') }}
          </el-button>
        </template>
      </el-table-column>
      <!-- <el-table-column align="center" :label="$t('profile.delete')" width="120">
        <template slot-scope="scope">
          <el-button
            v-if="!scope.row.roles.includes('admin')"
            v-permission="['manage user']"
            type="danger"
            size="small"
            icon="el-icon-delete"
            @click="handleDelete(scope.row.id, scope.row.name)"
          >
            {{ $t('profile.delete') }}
          </el-button>
        </template>
      </el-table-column> -->
    </el-table>

    <pagination
      v-show="total > 0"
      :total="total"
      :page.sync="query.page"
      :limit.sync="query.limit"
      style="text-align: right"
      @pagination="getList"
    />
    <!-- pop-up Edit User  -->
    <el-dialog
      :title="$t('profile.editUser')"
      :visible.sync="dialogVisible"
      width="400px"
      @close="closeDialog('ruleForm')"
    >
      <div class="form-container">
        <el-form
          ref="ruleForm"
          :model="ruleForm"
          :rules="rules"
          class="ruleForm"
          label-width="80px"
          style="max-width: 500px"
        >
          <el-form-item :label="$t('profile.name')" prop="name">
            <el-input v-model="ruleForm.name" />
          </el-form-item>
          <el-form-item :label="$t('profile.email')" prop="email">
            <el-input v-model="ruleForm.email" />
          </el-form-item>
          <!-- <el-form-item :label="$t('profile.role')">
            <el-select v-model="value">
              <el-option
                v-for="item in optionsRole"
                :key="item.value"
                :label="item.label"
                :value="item.value"
              />
            </el-select>
          </el-form-item> -->
        </el-form>
      </div>
      <span slot="footer" class="dialog-footer">
        <el-button @click="dialogVisible = false">{{
          $t('profile.cancel')
        }}</el-button>
        <el-button :disabled="isproces" type="primary" @click="confrimEdit()">{{
          $t('profile.confirm')
        }}</el-button>
      </span>
    </el-dialog>
    <!-- pop-up Create new user  -->
    <el-dialog
      width="500px"
      :title="$t('list.popCreateMew')"
      :visible.sync="dialogFormVisible"
    >
      <div v-loading="userCreating" class="form-container">
        <el-form
          ref="userForm"
          :rules="rules"
          :model="newUser"
          label-position="left"
          label-width="150px"
          style="max-width: 600px"
        >
          <!-- <el-form-item :label="$t('user.role')" prop="role">
            <el-select
              v-model="newUser.role"
              class="filter-item"
              :placeholder="$t('list.placeHolderRole')"
            >
              <el-option
                v-for="(item, index) in optionsRole"
                :key="index"
                :label="item.label | uppercaseFirst"
                :value="item.value"
              />
            </el-select>
          </el-form-item> -->
          <el-form-item :label="$t('user.name')" prop="name">
            <el-input v-model="newUser.name" />
          </el-form-item>
          <el-form-item :label="$t('user.email')" prop="email">
            <el-input v-model="newUser.email" />
          </el-form-item>
          <el-form-item :label="$t('user.password')" prop="password">
            <el-input v-model="newUser.password" show-password />
          </el-form-item>
          <el-form-item
            :label="$t('user.confirmPassword')"
            prop="confirmPassword"
          >
            <el-input v-model="newUser.confirmPassword" show-password />
          </el-form-item>
          <el-form-item>
            <el-input v-model="newUser.avatar" type="hidden" />
          </el-form-item>
        </el-form>
        <div slot="footer" class="dialog-footer">
          <el-button @click="dialogFormVisible = false">
            {{ $t('profile.cancel') }}
          </el-button>
          <el-button type="primary" @click="createUser()">
            {{ $t('profile.confirm') }}
          </el-button>
        </div>
      </div>
    </el-dialog>
  </div>
</template>

<script>
import Pagination from '@/components/Pagination'; // Secondary package based on el-pagination
import UserResource from '@/api/user';
import Resource from '@/api/resource';
import waves from '@/directive/waves'; // Waves directive
import permission from '@/directive/permission'; // Permission directive
import checkPermission from '@/utils/permission'; // Permission checking

const userResource = new UserResource();
const permissionResource = new Resource('permissions');

export default {
  name: 'UserList',
  components: { Pagination },
  directives: { waves, permission },
  data() {
    var validateConfirmPassword = (rule, value, callback) => {
      if (value !== this.newUser.password) {
        callback(new Error(this.$t('profile.messPassMismatched')));
      } else {
        callback();
      }
    };
    return {
      list: null,
      total: 0,
      loading: true,
      downloading: false,
      userCreating: false,
      query: {
        page: 1,
        limit: 15,
        keyword: '',
        roles: '',
        role: 'admin',
      },
      ruleForm: {
        type: Object,
        default: () => {
          return {
            id: undefined,
            name: '',
            email: '',
            roles: '',
            avatar: 'default-avatar.jpg',
          };
        },
      },
      dialogVisible: false,
      // roles: ['admin', 'user'],
      roles: ['admin'],
      // nonAdminRoles: ['user', 'admin'], // option of select roles
      newUser: {},
      dialogFormVisible: false,
      dialogPermissionVisible: false,
      dialogPermissionLoading: false,
      currentUserId: 0,
      currentUser: {
        name: '',
        permissions: [],
        rolePermissions: [],
      },
      rules: {
        role: [
          {
            required: true,
            message: this.$t('list.messRoleRequired'),
            trigger: 'change',
          },
        ],
        name: [
          {
            required: true,
            message: this.$t('list.messNameRequired'),
            trigger: 'blur',
          },
        ],
        email: [
          {
            required: true,
            message: this.$t('list.messEmailRequired'),
            trigger: 'blur',
          },
          {
            type: 'email',
            message: this.$t('list.messInputCorrectE'),
            trigger: ['blur', 'change'],
          },
        ],
        password: [
          {
            required: true,
            message: this.$t('list.messPasswordRequired'),
            trigger: 'blur',
          },
          { min: 6,
            message: this.$t('profile.messPassValLenght'),
            trigger: 'blur',
          },
        ],
        confirmPassword: [
          { validator: validateConfirmPassword, trigger: 'blur' },
        ],
      },
      permissionProps: {
        children: 'children',
        label: 'name',
        disabled: 'disabled',
      },
      permissions: [],
      menuPermissions: [],
      otherPermissions: [],
      isproces: false,
      activeActivity: 'first',
      updating: false,
      optionsRole: [
        {
          value: 'admin',
          label: this.$t('list.admin'),
        },
        {
          value: 'user',
          label: this.$t('list.user'),
        },
      ],
      value: 'user',
      last_page: undefined,
    };
  },
  computed: {
    language() {
      return this.$store.getters.language;
    },
    normalizedMenuPermissions() {
      let tmp = [];
      this.currentUser.permissions.role.forEach((permission) => {
        tmp.push({
          id: permission.id,
          name: permission.name,
          disabled: true,
        });
      });
      const rolePermissions = {
        id: -1, // Just a faked ID
        name: 'Inherited from role',
        disabled: true,
        children: this.classifyPermissions(tmp).menu,
      };

      tmp = this.menuPermissions.filter(
        (permission) =>
          !this.currentUser.permissions.role.find((p) => p.id === permission.id)
      );
      const userPermissions = {
        id: 0, // Faked ID
        name: 'Extra menus',
        children: tmp,
        disabled: tmp.length === 0,
      };

      return [rolePermissions, userPermissions];
    },
    normalizedOtherPermissions() {
      let tmp = [];
      this.currentUser.permissions.role.forEach((permission) => {
        tmp.push({
          id: permission.id,
          name: permission.name,
          disabled: true,
        });
      });
      const rolePermissions = {
        id: -1,
        name: 'Inherited from role',
        disabled: true,
        children: this.classifyPermissions(tmp).other,
      };

      tmp = this.otherPermissions.filter(
        (permission) =>
          !this.currentUser.permissions.role.find((p) => p.id === permission.id)
      );
      const userPermissions = {
        id: 0,
        name: 'Extra permissions',
        children: tmp,
        disabled: tmp.length === 0,
      };

      return [rolePermissions, userPermissions];
    },
    userMenuPermissions() {
      return this.classifyPermissions(this.userPermissions).menu;
    },
    userOtherPermissions() {
      return this.classifyPermissions(this.userPermissions).other;
    },
    userPermissions() {
      return this.currentUser.permissions.role.concat(
        this.currentUser.permissions.user
      );
    },
  },
  watch: {
    language() {
      this.optionsRole = [
        {
          value: 'admin',
          label: this.$t('list.admin'),
        },
      ];
    },
  },
  created() {
    // initialization
    this.resetNewUser();
    this.getList();
    if (checkPermission(['manage permission'])) {
      this.getPermissions();
    }
  },
  methods: {
    checkPermission,
    async getPermissions() {
      const { data } = await permissionResource.list({});
      const { all, menu, other } = this.classifyPermissions(data);
      this.permissions = all;
      this.menuPermissions = menu;
      this.otherPermissions = other;
    },
    openPop(row) {
      // open pop edit user
      this.dialogVisible = true;
      this.ruleForm = Object.assign({}, row);
      this.value = row.roles[0];
    },
    closeDialog(formName) {
      // Event popup close
      this.$refs[formName].resetFields();
      this.ruleForm.name = '';
      this.ruleForm.email = '';
      this.ruleForm.roles = ' ';
      this.value = '';
    },
    async getList() {
      // take the data
      const { limit, page } = this.query;
      this.loading = true;
      const { data, meta } = await userResource.list(this.query);
      this.list = data;
      this.list.forEach((element, index) => {
        element['index'] = (page - 1) * limit + index + 1;
      });
      this.total = meta.total;
      this.last_page = meta.last_page;
      this.lengthData = data.length;
      this.loading = false;
    },
    handleFilter() {
      this.getList();
    },
    handleCreate() {
      this.resetNewUser();
      this.dialogFormVisible = true;
      this.$nextTick(() => {
        this.$refs['userForm'].clearValidate();
      });
    },
    handleDelete(id, name) {
      this.$confirm(this.$t('list.cfDelete') + name, 'Warning', {
        confirmButtonText: 'OK',
        cancelButtonText: 'Cancel',
        type: 'warning',
      })
        .then(() => {
          userResource
            .destroy(id)
            .then((response) => {
              this.$message({
                type: 'success',
                message: this.$t('list.sucDelete'),
              });
              this.handleFilter();
            })
            .catch((error) => {
              console.log(error);
            });
        })
        .catch(() => {
          this.$message({
            type: 'info',
            message: this.$t('list.cancelDelete'),
          });
        });
    },
    async handleEditPermissions(id) {
      this.currentUserId = id;
      this.dialogPermissionLoading = true;
      this.dialogPermissionVisible = true;
      const found = this.list.find((user) => user.id === id);
      const { data } = await userResource.permissions(id);
      this.currentUser = {
        id: found.id,
        name: found.name,
        permissions: data,
      };
      this.dialogPermissionLoading = false;
      this.$nextTick(() => {
        this.$refs.menuPermissions.setCheckedKeys(
          this.permissionKeys(this.userMenuPermissions)
        );
        this.$refs.otherPermissions.setCheckedKeys(
          this.permissionKeys(this.userOtherPermissions)
        );
      });
    },
    createUser() {
      // create new  user
      this.$refs['userForm'].validate((valid) => {
        if (valid) {
          this.newUser.roles = [this.newUser.role];
          this.userCreating = true;
          this.newUser.avatar = 'default-avatar.jpg';
          userResource
            .store(this.newUser, this.query.limit)
            .then((response) => {
              this.$message({
                message:
                  this.$t('list.messCfNewUserFont') +
                  this.newUser.name +
                  '(' +
                  this.newUser.email +
                  this.$t('list.messCfNewUserTail'),
                type: 'success',
                duration: 5 * 1000,
              });
              this.query.page = response.page_infor.last_page;
              this.total = response.page_infor.total;
              this.last_page = response.page_infor.last_page;
              this.list = response.page_infor.data;

              this.resetNewUser();
              this.dialogFormVisible = false;
            })
            .catch((error) => {
              console.log(error);
            })
            .finally(() => {
              this.userCreating = false;
              this.dialogFormVisible = false;
            });
        } else {
          return false;
        }
      });
    },
    resetNewUser() {
      this.newUser = {
        name: '',
        email: '',
        password: '',
        confirmPassword: '',
        role: 'admin',
      };
    },
    handleDownload() {
      this.downloading = true;
      import('@/vendor/Export2Excel').then((excel) => {
        const tHeader = ['id', 'user_id', 'name', 'email', 'role'];
        const filterVal = ['index', 'id', 'name', 'email', 'role'];
        const data = this.formatJson(filterVal, this.list);
        excel.export_json_to_excel({
          header: tHeader,
          data,
          filename: 'user-list',
        });
        this.downloading = false;
      });
    },
    formatJson(filterVal, jsonData) {
      return jsonData.map((v) => filterVal.map((j) => v[j]));
    },
    permissionKeys(permissions) {
      return permissions.map((permssion) => permssion.id);
    },
    classifyPermissions(permissions) {
      const all = [];
      const menu = [];
      const other = [];
      permissions.forEach((permission) => {
        const permissionName = permission.name;
        all.push(permission);
        if (permissionName.startsWith('view menu')) {
          menu.push(this.normalizeMenuPermission(permission));
        } else {
          other.push(this.normalizePermission(permission));
        }
      });
      return { all, menu, other };
    },

    normalizeMenuPermission(permission) {
      return {
        id: permission.id,
        name: this.$options.filters.uppercaseFirst(
          permission.name.substring(10)
        ),
        disabled: permission.disabled || false,
      };
    },

    normalizePermission(permission) {
      const disabled =
        permission.disabled || permission.name === 'manage permission';
      return {
        id: permission.id,
        name: this.$options.filters.uppercaseFirst(permission.name),
        disabled: disabled,
      };
    },

    confirmPermission() {
      const checkedMenu = this.$refs.menuPermissions.getCheckedKeys();
      const checkedOther = this.$refs.otherPermissions.getCheckedKeys();
      const checkedPermissions = checkedMenu.concat(checkedOther);
      this.dialogPermissionLoading = true;

      userResource
        .updatePermission(this.currentUserId, {
          permissions: checkedPermissions,
        })
        .then((response) => {
          this.$message({
            message: 'Permissions has been updated successfully',
            type: 'success',
            duration: 5 * 1000,
          });
          this.dialogPermissionLoading = false;
          this.dialogPermissionVisible = false;
        });
    },
    confrimEdit() {
      // submit when click Confrim
      this.$refs['ruleForm'].validate((valid) => {
        if (valid) {
          this.$isproces = true;
          this.ruleForm.roles.splice(0, 1, this.value);
          userResource
            .update(this.ruleForm.id, {
              name: this.ruleForm.name,
              email: this.ruleForm.email,
              roles: this.ruleForm.roles,
              current_page: this.query.page,
            })
            .then((response) => {
              this.isproces = false;
              this.dialogVisible = false;
              this.$message({
                message: 'User information has been updated successfully',
                type: 'success',
              });
            })
            .catch((error) => {
              console.log(error);
              this.isproces = false;
            });
          this.handleFilter();
          this.dialogVisible = false;
        }
      });
    },
  },
};
</script>

<style lang="scss" scoped>
.edit-input {
  padding-right: 100px;
}
.cancel-btn {
  position: absolute;
  right: 15px;
  top: 10px;
}
.dialog-footer {
  text-align: left;
  padding-top: 0;
  margin-left: 150px;
}
.app-container {
  flex: 1;
  justify-content: space-between;
  font-size: 14px;
  padding-right: 8px;
  .block {
    float: left;
    min-width: 250px;
  }
  .clear-left {
    clear: left;
  }
  .inputClassname {
    margin-top: 35px;
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
  .el-input__inner{
    margin-top:15px ;
  }
}
</style>

<template>
  <div class="app-container">
    <el-form>
      <el-row :gutter="20">
        <el-col :span="6">
          <el-card>
            <!--  container avatar -->
            <div class="user-profile">
              <div class="user-avatar box-center">
                <pan-thumb :image="user.avatar" :height="'100px'" :width="'100px'" :hoverable="false" />
              </div>
              <div class="user-follow">
                <el-button type="primary" style="width: 100%;" @click="showDialog">
                  {{ $t('profile.editAva') }}
                </el-button>
              </div>
              <div class="box-center">
                <div class="user-name text-center">
                  {{ user.name }}
                </div>
                <div class="user-role text-center text-muted">
                  {{ user.roles[0] }}
                </div>
              </div>

            </div>
          </el-card>
          <!-- pop-up change avatar -->
          <el-dialog
            :title="$t('profile.uploadAva')"
            :visible.sync="dialogVisible"
            width="40%"
          >
            <center>
              <div class="upload">
                <button class="btn"><i class="el-icon-upload2" />{{ $t('profile.uploadImg') }}</button>
                <input id="pic_avatar" type="file" accept="image/*" @change="preview_avatar">
              </div>
            </center>
            <div id="preview_avatar">
              <center>
                <el-image
                  style="width: 200px; height: 200px; margin-top: 10px; margin-botton: 10px;"
                  :src="ava"
                  fit
                />
              </center>
            </div>
            <span slot="footer" class="dialog-footer">
              <el-button @click="dialogVisible = false">{{ $t('profile.cancel') }}</el-button>
              <el-button type="primary" @click="confrim()">{{ $t('profile.confirm') }}</el-button>
            </span>
          </el-dialog>
        </el-col>
        <el-col :span="18">
          <!-- container profile user -->
          <el-card>
            <el-tabs v-model="activeActivity">
              <el-tab-pane v-loading="updating" :label="$t('profile.account')" name="first">
                <el-form-item :label="$t('profile.name')">
                  <el-input v-model="user.name" />
                </el-form-item>
                <el-form-item :label="$t('profile.email')">
                  <el-input v-model="user.email" />
                </el-form-item>
                <el-form-item>
                  <el-button type="primary" @click="onSubmit">
                    {{ $t('profile.update') }}
                  </el-button>
                </el-form-item>
              </el-tab-pane>
              <el-tab-pane v-loading="updating" :label="$t('profile.changePassword')">
                <el-form ref="userForm" :rules="rules" :model="userForm" label-position="left" label-width="200px" style="max-width: 500px;">
                  <el-form-item :label="$t('profile.currentPassword')" prop="password">
                    <el-input v-model="userForm.password" show-password />
                  </el-form-item>
                  <el-form-item :label="$t('profile.newPassword')" prop="newPassword">
                    <el-input v-model="userForm.newPassword" show-password pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,}" />
                  </el-form-item>
                  <el-form-item :label="$t('profile.confirmNewPassword')" prop="confirmPassword">
                    <el-input v-model="userForm.confirmPassword" show-password />
                  </el-form-item>
                </el-form>
                <el-button type="primary" @click="chagePassword">
                  {{ $t('profile.update') }}
                </el-button>
              </el-tab-pane>
            </el-tabs>
          </el-card>

        </el-col>
      </el-row>
    </el-form>
  </div>
</template>

<script>
import PanThumb from '@/components/PanThumb';
import { UploadAvatar, getProfile, update_password } from '@/api/profile';
import UserResource from '@/api/user';
const userResource = new UserResource();
export default {
  name: 'SelfProfile',
  components: { PanThumb },

  data() {
    var validateCurrentPassword = (rule, value, callback) => {
      if (value === '') {
        callback(new Error(this.$t('profile.messPassRequire')));
      } else if (value < 6){
        callback(new Error(this.$t('profile.messPassValLenght')));
      } else {
        callback();
      }
    };
    var validateConfirmPassword = (rule, value, callback) => {
      if (value !== this.userForm.newPassword) {
        callback(new Error(this.$t('profile.messPassMismatched')));
      } else {
        callback();
      }
    };
    var validateNewPassword = (rule, value, callback) => {
      if (value < 6) {
        callback(new Error(this.$t('profile.messPassValLenght')));
      } else {
        callback();
      }
    };
    return {
      users: {},
      x: 0,
      userForm: {
        password: '',
        newPassword: '',
        confirmPassword: '',
      },
      user:
        {
          id: 0,
          email: '',
          avatar: '',
          roles: ' ',
          name: ' ',
        },
      newPass: {},
      rules: {
        password: [{ validator: validateCurrentPassword, trigger: 'blur' }, { min: 6, message: this.$t('profile.messPassValLenght'), trigger: 'blur' }],
        confirmPassword: [{ validator: validateConfirmPassword, trigger: 'blur' }, { min: 6, message: this.$t('profile.messPassValLenght'), trigger: 'blur' }],
        newPassword: [{ validator: validateNewPassword, trigger: 'blur' }, { min: 6, message: this.$t('profile.messPassValLenght'), trigger: 'blur' }],
      },
      dialogVisible: false,
      avatarTaken: '',
      ava: ' ',
      activeActivity: 'first',
      updating: false,
      options: [{
        value: 'admin',
        label: 'Admin',
      }, {
        value: 'user',
        label: 'User',
      }],
      value: '',
    };
  },
  watch: {
    '$route': 'getUser',
  },
  created() {
    this.getData();
  },
  methods: {
    resetPass(){
      this.userForm = {
        password: '',
        newPassword: '',
        confirmPassword: '',
      };
    },
    onSubmit() { // Submit change profile user
      this.updating = false;
      this.updating = true;
      userResource
        .update(this.user.id, this.user)
        .then(response => {
          this.updating = false;
          this.$message({
            message: this.$t('profile.messUpdateSuccess'),
            type: 'success',
            duration: 5 * 1000,
          });
        })
        .catch(error => {
          console.log(error);
          this.updating = false;
        });
    },
    chagePassword(){ // Submit change password user
      this.$refs['userForm'].validate((valid) => {
        if (valid){
          var data = {
            id: this.user.id,
            password: this.userForm.password,
            newPassword: this.userForm.newPassword,
          };
          if (this.userForm.newPassword === this.userForm.confirmPassword){
            const resetPass = update_password(data);
            this.updating = false;
            resetPass.then((response) => {
              this.$message({
                message: this.$t('profile.messUpdateSuccess'),
                type: 'success',
              });
              this.updating = false;
              this.resetPass();
            });
          } else {
            this.resetPass();
            this.$message({
              message: this.$t('profile.messPassMismatched'),
              type: 'error',
            });
            this.updating = false;
          }
        }
      });
    },
    confrim(){ // Submit change avatar
      if (document.getElementById('pic_avatar').files[0] !== undefined){
        var newAva = new FormData();
        newAva.append('avatar', document.getElementById('pic_avatar').files[0]);
        newAva.append('id', this.$store.getters.userId);
        UploadAvatar(newAva);
        this.dialogVisible = false;
        this.$message({
          message: this.$t('profile.messUpdateSuccess'),
          type: 'success',
        });
      } else {
        this.$message.error(this.$t('profile.messUpdateFail'));
      }
    },
    showDialog(){ // Open pop up change avatar
      this.ava = this.user.avatar;
      this.dialogVisible = true;
    },
    preview_avatar(e) { // preview avatar after change avatar
      const file_correct = event.target.files[0];
      this.ava = URL.createObjectURL(file_correct);
      this.user.avatar = this.ava;
    },
    getData(){ // take data of user
      var dataTaken = getProfile({ id: this.$store.getters.userId });
      dataTaken.then((data) => {
        this.user.id = this.$store.getters.userId;
        this.user.name = data.data.name;
        this.user.email = data.data.email;
        this.user.avatar = process.env.MIX_STORE_IMAGE_URL + 'avatars/' + data.data.avatar;
        this.user.roles = data.data.roles;
        this.user.password = data.data.password;
      });
    },
  },
};
</script>

<style lang="scss" scoped>
.user-profile {
  .user-name {
    font-weight: bold;
  }
  .box-center {
    padding-top: 10px;
  }
  .user-role {
    padding-top: 10px;
    font-weight: 400;
    font-size: 14px;
  }
  .box-social {
    padding-top: 30px;
    .el-table {
      border-top: 1px solid #dfe6ec;
    }
  }
  .user-follow {
    padding-top: 20px;
  }
}
.user-activity {
  .user-block {
    .username, .description {
      display: block;
      margin-left: 50px;
      padding: 2px 0;
    }
    img {
      width: 40px;
      height: 40px;
      float: left;
    }
    :after {
      clear: both;
    }
    .img-circle {
      border-radius: 50%;
      border: 2px solid #d2d6de;
      padding: 2px;
    }
    span {
      font-weight: 500;
      font-size: 12px;
    }
  }
  .post {
    font-size: 14px;
    border-bottom: 1px solid #d2d6de;
    margin-bottom: 15px;
    padding-bottom: 15px;
    color: #666;
    .image {
      width: 100%;
    }
    .user-images {
      padding-top: 20px;
    }
  }
  .list-inline {
    padding-left: 0;
    margin-left: -5px;
    list-style: none;
    li {
      display: inline-block;
      padding-right: 5px;
      padding-left: 5px;
      font-size: 13px;
    }
    .link-black {
      &:hover, &:focus {
        color: #999;
      }
    }
  }
  .el-carousel__item h3 {
    color: #475669;
    font-size: 14px;
    opacity: 0.75;
    line-height: 200px;
    margin: 0;
  }

  .el-carousel__item:nth-child(2n) {
    background-color: #99a9bf;
  }

  .el-carousel__item:nth-child(2n+1) {
    background-color: #d3dce6;
  }
}
div.upload {
  position: relative;
  overflow: hidden;
  display: inline-block;
  width: 100%;
  text-align: center;
  margin-top: 10px;
}
.btn {
  border: none;
  background-color: #409EFF;
  color: white;
  padding: 8px 20px;
  font-weight: 500;
  border-radius: 4px;
  font-size: 14px;
  width: 50%;
}
.upload input[type=file] {
  position: absolute;
  left: 0;
  top: 0;
  opacity: 0;
  width: 100%;
}
</style>

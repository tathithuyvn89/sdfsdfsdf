<template>
  <div ref="test" class="app-container">
    <div class="filter-container">
      <el-button type="success" @click="handleCreate()">{{
        $t('button.create')
      }}</el-button>
      <el-button type="danger" @click="handleDeleteManyQuiz()">{{
        $t('button.delete')
      }}</el-button>
      <el-input
        v-model="listQuery.keyword"
        :placeholder="$t('placeholder.search')"
        class="inputSearch"
        @keyup.enter.native="handleFilter"
      />
    </div>

    <el-table
      :key="tableKey"
      ref="tableQuiz"
      v-loading="listLoading"
      :data="ListQuiz"
      border
      fit
      style="width: 100%"
      class="tableList"
      @sort-change="sortTableChange"
      @selection-change="handleSelectionChange"
    >
      <el-table-column type="selection" width="55" align="center" />
      <el-table-column
        :label="$t('table.col_title')"
        align="center"
        min-width="300"
      >
        <template slot-scope="scope">
          <span>{{ scope.row.title }}</span>
        </template>
      </el-table-column>

      <el-table-column
        :label="$t('table.col_picture')"
        align="center"
        min-width="150"
      >
        <template slot-scope="scope">
          <div v-if="scope.row.picture === null || scope.row.picture === ''">
            <div slot="error" class="image-slot" style="font-size: 78px">
              <i class="el-icon-picture-outline" />
            </div>
          </div>
          <div v-else>
            <el-image
              style="width: 30%"
              :src="domain + scope.row.picture"
              fit="fill"
            />
          </div>
        </template>
      </el-table-column>

      <el-table-column
        :label="$t('table.col_option')"
        align="center"
        min-width="150"
      >
        <template slot-scope="scope">
          <el-popover placement="bottom" width="400" trigger="hover">
            <el-table :data="scope.row.options">
              <el-table-column
                :label="$t('button.options')"
                max-width="400"
                property="option"
              />
            </el-table>
            <el-button slot="reference">{{ $t('button.options') }}</el-button>
          </el-popover>
        </template>
      </el-table-column>

      <el-table-column
        :label="$t('table.col_content')"
        align="center"
        min-width="300"
      >
        <template slot-scope="scope">
          <span>{{ scope.row.sentence }}</span>
        </template>
      </el-table-column>

      <el-table-column
        :label="$t('table.col_showdetail')"
        align="center"
        min-width="150"
      >
        <template slot-scope="{ row }">
          <el-button @click="handleShow(row)">{{
            $t('button.show')
          }}</el-button>
        </template>
      </el-table-column>

      <el-table-column
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
            type="danger"
            icon="el-icon-delete"
            circle
            @click="handleDeleteOneQuiz(row)"
          />
        </template>
      </el-table-column>
    </el-table>

    <!-- Pagination -->
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
      title="Create/Edit quiz"
      :visible.sync="dialogVisible"
      :fullscreen="fullscreen"
      :close-on-click-modal="false"
      :close-on-press-escape="false"
      @close="closeDialog('ruleform')"
    >
      <div slot="title">
        <el-row>
          <el-col
            :span="12"
          ><div class="left">
            <h1>{{ Title }}</h1>
          </div></el-col>
          <el-col
            :span="12"
          ><div class="right">
            <svg-icon :icon-class="iconfullscreen" @click="showfull()" /></div></el-col>
        </el-row>
      </div>
      <el-form
        ref="ruleform"
        :model="ruleForm"
        :rules="rules"
        class="ruleForm"
        :readonly="tempAction.hidden"
      >
        <p class="custom-tile">{{ $t('popup.quiz.tTitle') }}</p>
        <el-row>
          <el-col :span="11" class="test">
            <el-form-item prop="title_ja">
              <el-input
                v-model="ruleForm.title_ja"
                :placeholder="$t('popup.quiz.pEnterJapanese')"
                :readonly="tempAction.hidden"
              />
            </el-form-item>
          </el-col>
          <el-col :span="2"><div class="space" /></el-col>
          <el-col :span="11">
            <el-form-item prop="title_en" class="test">
              <el-input
                v-model="ruleForm.title_en"
                :placeholder="$t('popup.quiz.pEnterEnglish')"
                :readonly="tempAction.hidden"
              />
            </el-form-item>
          </el-col>
        </el-row>

        <p class="custom-tile">{{ $t('popup.quiz.tSentence') }}</p>
        <el-row>
          <el-col :span="11">
            <el-form-item prop="sentence_ja">
              <el-input
                v-model="ruleForm.sentence_ja"
                style="word-break: normal"
                type="textarea"
                :rows="3"
                :placeholder="$t('popup.quiz.pEnterJapanese')"
                :readonly="tempAction.hidden"
              />
            </el-form-item>
          </el-col>
          <el-col :span="2"><div class="space" /></el-col>
          <el-col :span="11">
            <el-form-item prop="sentence_en">
              <el-input
                v-model="ruleForm.sentence_en"
                style="word-break: normal"
                type="textarea"
                :rows="3"
                :placeholder="$t('popup.quiz.pEnterEnglish')"
                :readonly="tempAction.hidden"
              />
            </el-form-item>
          </el-col>
        </el-row>

        <p class="custom-tile">{{ $t('popup.quiz.tPicture') }}</p>
        <el-row>
          <el-col :span="11">
            <input
              id="pic_ja"
              type="file"
              accept="image/*"
              :disabled="tempAction.hidden"
              style="display: none"
              @change="preView_ja"
            >
            <el-button
              type="primary"
            ><label
              type="button"
              for="pic_ja"
            >Upload Picture</label></el-button>
            <!--=============== Show picture of Quizz ====================-->
            <div id="preview">
              <div v-if="img_ja === null">
                <div slot="error" style="font-size: 100px" class="image-slot">
                  <i class="el-icon-picture-outline" />
                </div>
              </div>
              <div v-else class="image-preview-wrapper">
                <div class="image_form_quiz">
                  <img :src="img_ja">
                </div>
                <div v-if="Title !== 'Show quiz'" class="image-preview-action">
                  <button>
                    <i class="el-icon-delete" @click="rmImage(img_ja)" />
                  </button>
                </div>
              </div>
            </div>
          </el-col>
          <el-col :span="2"><div class="space" /></el-col>
          <el-col :span="11">
            <input
              id="pic_en"
              type="file"
              accept="image/*"
              :disabled="tempAction.hidden"
              style="display: none"
              @change="preView_en"
            >
            <el-button
              type="primary"
            ><label for="pic_en">Upload Picture</label></el-button>
            <div id="preview">
              <div v-if="img_en === null">
                <div slot="error" style="font-size: 100px" class="image-slot">
                  <i class="el-icon-picture-outline" />
                </div>
              </div>
              <div v-else class="image-preview-wrapper">
                <div class="image_form_quiz">
                  <img :src="img_en">
                </div>
                <div v-if="Title !== 'Show quiz'" class="image-preview-action">
                  <button>
                    <i class="el-icon-delete" @click="rmImage(img_en)" />
                  </button>
                </div>
              </div>
            </div>
          </el-col>
        </el-row>

        <p class="custom-tile">{{ $t('popup.quiz.tOptions') }}</p>
        <el-table
          ref="dragTable"
          :data="ruleForm.options"
          row-key="position"
          border
          fit
          stripe
          highlight-current-row
          style="width: 100%"
          class="tableInput"
        >
          <el-table-column
            align="center"
            :label="$t('popup.quiz.table.Sort')"
            width="100px"
          >
            <el-button
              type="danger"
              icon="el-icon-rank"
              circle
              class="handle"
              :disabled="tempAction.hidden == true"
            />
          </el-table-column>

          <el-table-column
            :label="$t('popup.quiz.table.Japanese')"
            align="center"
          >
            <template slot-scope="scope">
              <el-input
                v-model="scope.row.option_ja"
                :placeholder="$t('popup.quiz.pEnterJapanese')"
                :readonly="tempAction.hidden"
                @change="textChange(scope.$index)"
              />
            </template>
          </el-table-column>
          <el-table-column
            :label="$t('popup.quiz.table.English')"
            align="center"
          >
            <template slot-scope="scope">
              <el-input
                v-model="scope.row.option_en"
                :placeholder="$t('popup.quiz.pEnterEnglish')"
                :readonly="tempAction.hidden"
                @change="textChange(scope.$index)"
              />
            </template>
          </el-table-column>

          <el-table-column
            :label="$t('popup.quiz.table.Correct')"
            align="center"
            width="100px"
          >
            <template slot-scope="scope">
              <el-checkbox
                v-model="scope.row.correct"
                :disabled="tempAction.hidden"
                @change="handletableCorrect(scope.row)"
              />
            </template>
          </el-table-column>

          <el-table-column
            v-if="tempAction.hidden == false"
            align="center"
            :label="$t('popup.quiz.table.Delete')"
            width="100px"
          >
            <template slot-scope="scope">
              <el-button
                type="danger"
                icon="el-icon-delete"
                circle
                @click="handleDeleteOption(scope.$index)"
              />
            </template>
          </el-table-column>
        </el-table>

        <center v-if="tempAction.hidden == false">
          <el-row>
            <el-button
              type="danger"
              class="btn-insert"
              icon="el-icon-plus"
              circle
              @click="insertRow()"
            />
          </el-row>
        </center>

        <p class="custom-tile">{{ $t('popup.quiz.tExCorrect') }}</p>
        <el-row>
          <el-col :span="11">
            <el-form-item prop="explain_correct_ja">
              <el-input
                v-model="ruleForm.explain_correct_ja"
                style="word-break: normal"
                type="textarea"
                :rows="2"
                :placeholder="$t('popup.quiz.pEnterJapanese')"
                :readonly="tempAction.hidden"
              />
            </el-form-item>
          </el-col>
          <el-col :span="2"><div class="space" /></el-col>
          <el-col :span="11">
            <el-form-item prop="explain_correct_en">
              <el-input
                v-model="ruleForm.explain_correct_en"
                style="word-break: normal"
                type="textarea"
                :rows="2"
                :placeholder="$t('popup.quiz.pEnterEnglish')"
                :readonly="tempAction.hidden"
              />
            </el-form-item>
          </el-col>
        </el-row>

        <p class="custom-tile">{{ $t('popup.quiz.tExIncorrect') }}</p>
        <el-row>
          <el-col :span="11">
            <el-form-item prop="explain_incorrect_ja">
              <el-input
                v-model="ruleForm.explain_incorrect_ja"
                style="word-break: normal"
                type="textarea"
                :rows="2"
                :placeholder="$t('popup.quiz.pEnterJapanese')"
                :readonly="tempAction.hidden"
              />
            </el-form-item>
          </el-col>
          <el-col :span="2"><div class="space" /></el-col>
          <el-col :span="11">
            <el-form-item prop="explain_incorrect_en">
              <el-input
                v-model="ruleForm.explain_incorrect_en"
                style="word-break: normal"
                type="textarea"
                :rows="2"
                :placeholder="$t('popup.quiz.pEnterEnglish')"
                :readonly="tempAction.hidden"
              />
            </el-form-item>
          </el-col>
        </el-row>
      </el-form>

      <div slot="footer" class="dialog-footer">
        <el-button @click="closeDialog('ruleform')">
          {{ $t('button.close') }}
        </el-button>
        <el-button
          v-if="tempAction.hidden == false"
          type="primary"
          :disabled="isproces"
          @click="submit()"
        >
          {{ $t('button.save') }}
        </el-button>
      </div>
    </el-dialog>

    <el-dialog
      :title="$t('popup.quiz.delete.title')"
      :visible.sync="dialogVisibleDelete"
      center
      width="30%"
    >
      <center>{{ $t('popup.quiz.delete.content') }}</center>
      <div slot="footer" class="dialog-footer">
        <el-button @click="dialogVisibleDelete = false">
          {{ $t('popup.quiz.delete.close') }}
        </el-button>
        <el-button type="danger" @click="submitConfrimDelete()">
          {{ $t('popup.quiz.delete.Delete') }}
        </el-button>
      </div>
    </el-dialog>
  </div>
</template>

<script>
import Sortable from 'sortablejs'; // import table drap for position options
import {
  fetchList,
  fetchData,
  addQuiz,
  deleteQuiz,
  editQuiz,
} from '@/api/quiz'; // import file call api
const DELETE_IMAGE = 3;
export default {
  data() {
    return {
      // pagination
      total: 0,
      tableKey: 0,
      last_page: 0,
      // query call page pagination -- la for refeash page after actions
      listQuery: {
        page: 1,
        keyword: '',
        la: this.$root.$i18n.locale,
      },
      listLoading: true, // loading lazy table
      ListQuiz: [], // array store list quiz
      multipleSelection: [], // array store id quiz want to delete
      dialogVisible: false, // property for dialog create/edit/show
      dialogVisibleDelete: false, // property for dialog cf delete
      ruleForm: {
        // obj store data in popup create/edit/sho
        title_ja: '',
        title_en: '',
        sentence_ja: '',
        sentence_en: '',
        picture_ja: '',
        picture_en: '',
        options: [],
        explain_correct_en: '',
        exCorrect_ja: '',
        explain_incorrect_en: '',
        exIncorrect_ja: '',
      },
      img_en: '',
      img_ja: '',
      rules: {
        // rules for validate form in popup
        title_ja: [
          {
            required: true,
            message: this.$t('popup.quiz.validate.rtitle'),
            trigger: 'blur',
          },
          {
            max: 255,
            message: this.$t('popup.quiz.validate.ltitle'),
            trigger: 'blur',
          },
        ],
        title_en: [
          {
            required: true,
            message: this.$t('popup.quiz.validate.rtitle'),
            trigger: 'blur',
          },
          {
            max: 255,
            message: this.$t('popup.quiz.validate.ltitle'),
            trigger: 'blur',
          },
        ],
        sentence_ja: [
          {
            required: true,
            message: this.$t('popup.quiz.validate.rsentence'),
            trigger: 'blur',
          },
          {
            max: 5000,
            message: this.$t('popup.quiz.validate.lsentence'),
            trigger: 'blur',
          },
        ],
        sentence_en: [
          {
            required: true,
            message: this.$t('popup.quiz.validate.rsentence'),
            trigger: 'blur',
          },
          {
            max: 5000,
            message: this.$t('popup.quiz.validate.lsentence'),
            trigger: 'blur',
          },
        ],
        explain_correct_ja: [
          {
            required: true,
            message: this.$t('popup.quiz.validate.rexCorrect'),
            trigger: 'blur',
          },
          {
            max: 5000,
            message: this.$t('popup.quiz.validate.lexCorrect'),
            trigger: 'blur',
          },
        ],
        explain_correct_en: [
          {
            required: true,
            message: this.$t('popup.quiz.validate.rexCorrect'),
            trigger: 'blur',
          },
          {
            max: 5000,
            message: this.$t('popup.quiz.validate.lexCorrect'),
            trigger: 'blur',
          },
        ],
        explain_incorrect_ja: [
          {
            required: true,
            message: this.$t('popup.quiz.validate.rexIncorrect'),
            trigger: 'blur',
          },
          {
            max: 5000,
            message: this.$t('popup.quiz.validate.lexIncorrect'),
            trigger: 'blur',
          },
        ],
        explain_incorrect_en: [
          {
            required: true,
            message: this.$t('popup.quiz.validate.rexIncorrect'),
            trigger: 'blur',
          },
          {
            max: 5000,
            message: this.$t('popup.quiz.validate.lexIncorrect'),
            trigger: 'blur',
          },
        ],
      },
      sortable: null,
      tempAction: {
        // Store action and disabel textbox when show
        Action: '',
        hidden: false,
      },
      isproces: false, // variable for is process
      lengthData: undefined, // check change current page when delete quiz
      temp: [], // array store data quiz want delete one
      arrDelete: [], // arr store options delete
      tempPosition: [], // arr store postions
      Title: '', // binding title create, edit and show
      fullscreen: false, // binding show full screen
      iconfullscreen: 'fullscreen', // binding icon fullscreen
      domain: process.env.MIX_STORE_IMAGE_URL,
      listIdDeleteFaild: [],
      listIndexDeleteFaild: [],
    };
  },
  computed: {
    // refeash page when change languages
    i18nLang() {
      return this.$root.$i18n.locale;
    },
    listIdDeleteFaildChange() {
      return this.listIdDeleteFaild;
    },
  },
  watch: {
    i18nLang() {
      this.listQuery.la = this.$root.$i18n.locale;
      this.getList();
    },
    listIdDeleteFaildChange() {
      this.listIndexDeleteFaild = this.getListIndexDeleteFaild();
      this.toggleTableRowClassName(this.$refs.tableQuiz.$el, this.listIndexDeleteFaild);
    },
  },
  created() {
    this.getList(); // call page 1 when loaded
  },
  methods: {
    // Method remove image
    rmImage(img_value) {
      if (img_value === this.img_en) {
        // this.ruleForm.picture_en = null;
        this.img_en = null;
      }
      if (img_value === this.img_ja) {
        // this.ruleForm.picture_ja = null;
        this.img_ja = null;
      }
    },
    showfull() {
      // methods change title.icon when create/edit/show
      if (this.fullscreen === false) {
        this.fullscreen = true;
        this.iconfullscreen = 'exit-fullscreen';
      } else {
        this.fullscreen = false;
        this.iconfullscreen = 'fullscreen';
      }
    },
    preView_en(e) {
      const file_en = event.target.files[0];
      this.img_en = URL.createObjectURL(file_en);
    },
    preView_ja(e) {
      const file_ja = event.target.files[0];

      this.img_ja = URL.createObjectURL(file_ja);
    },
    async getList() {
      // Get data in page
      this.listLoading = true;
      const data = await fetchList(this.listQuery);
      this.ListQuiz = data.data;
      for (var i = 0; i < this.ListQuiz.length; i++) {
        for (var j = 0; j < this.ListQuiz[i].options.length; j++) {
          this.ListQuiz[i].options[j].status = 0;
        }
      }
      this.total = data.total;
      this.listQuery.page = data.current_page;
      this.last_page = data.last_page;
      this.lengthData = data.data.length;
      this.listLoading = false;
    },
    setSort() {
      // drap row in table options
      const el = this.$refs.dragTable.$el.querySelectorAll(
        '.el-table__body-wrapper > table > tbody'
      )[0];
      this.sortable = Sortable.create(el, {
        handle: '.handle', // handle's class
        animation: 150,
        ghostClass: 'sortable-ghost', // Class when sorting quiz
        onEnd: (evt) => {
          // Rearranges elements in array
          const targetRow = this.ruleForm.options.splice(evt.oldIndex, 1)[0];
          this.ruleForm.options.splice(evt.newIndex, 0, targetRow);
        },
      });
    },
    handleFilter() {
      // Get date when search
      this.listQuery.page = 1;
      this.getList();
    },
    toggleSelection(rows) {
      // Choose the right question
      if (rows) {
        rows.forEach((row) => {
          this.$refs.multipleTable.toggleRowSelection(row);
        });
      } else {
        this.$refs.multipleTable.clearSelection();
      }
    },
    handleSelectionChange(val) {
      // methods when click btn create
      this.multipleSelection = [];
      for (var i = 0; i < val.length; i++) {
        this.multipleSelection.push(val[i].id);
      }
    },
    handleCreate() {
      // Event click Create
      this.tempAction.Action = 'Create';
      this.Title = this.$t('popup.quiz.titleCreate');
      this.tempAction.hidden = false;
      this.dialogVisible = true;
      this.img_en = null;
      this.img_ja = null;
      this.$nextTick(() => {
        this.setSort();
      });
    },
    submit() {
      // Prevent many message can show
      this.$message.closeAll();
      // method when click btn save in popup
      this.$refs['ruleform'].validate((valid) => {
        // for check validate
        if (valid) {
          if (this.tempAction.Action === 'Create') {
            // check create
            this.ruleForm.picture_ja = document.getElementById(
              'pic_ja'
            ).files[0];

            this.ruleForm.picture_en = document.getElementById(
              'pic_en'
            ).files[0];

            var checkOption = false;
            for (var x = 0; x < this.ruleForm.options.length; x++) {
              if (
                this.ruleForm.options[x].option_ja === '' ||
                this.ruleForm.options[x].option_en === ''
              ) {
                checkOption = true;
                break;
              }
            }
            if (checkOption === false && this.ruleForm.options.length >= 1) {
              var count = 0;
              if (this.tempAction.Action === 'Create') {
                for (var t = 0; t < this.ruleForm.options.length; t++) {
                  if (this.ruleForm.options[t].correct === false) {
                    count++;
                  }
                }
              }
              if (this.tempAction.Action === 'Edit') {
                for (var o = 0; o < this.ruleForm.options.length; o++) {
                  if (this.ruleForm.options[o].correct === 0) {
                    count++;
                  }
                }
              }

              if (count !== this.ruleForm.options.length) {
                this.isproces = true;
                var options = this.ruleForm.options;
                for (var a = 0; a < options.length; a++) {
                  options[a].position = a + 1;
                }
                for (var i = 0; i < options.length; i++) {
                  if (options[i].correct === false) {
                    options[i].correct = 0;
                  }
                  if (options[i].correct === true) {
                    options[i].correct = 1;
                  }
                }
                var file = new FormData();
                if (
                  this.img_ja !== null &&
                  document.getElementById('pic_ja').files.length === 1
                ) {
                  file.append('picture_ja', this.ruleForm.picture_ja);
                }
                if (
                  this.img_en !== null &&
                  document.getElementById('pic_en').files.length === 1
                ) {
                  file.append('picture_en', this.ruleForm.picture_en);
                }

                file.append('title_ja', this.ruleForm.title_ja);
                file.append('title_en', this.ruleForm.title_en);
                file.append('sentence_ja', this.ruleForm.sentence_ja);
                file.append('sentence_en', this.ruleForm.sentence_en);
                file.append(
                  'explain_correct_ja',
                  this.ruleForm.explain_correct_ja
                );
                file.append(
                  'explain_correct_en',
                  this.ruleForm.explain_correct_en
                );
                file.append(
                  'explain_incorrect_ja',
                  this.ruleForm.explain_incorrect_ja
                );
                file.append(
                  'explain_incorrect_en',
                  this.ruleForm.explain_incorrect_en
                );
                for (var j = 0; j < options.length; j++) {
                  file.append(
                    'options' + '[' + j + '][option_ja]',
                    options[j].option_ja
                  );
                  file.append(
                    'options' + '[' + j + '][option_en]',
                    options[j].option_en
                  );
                  file.append(
                    'options' + '[' + j + '][correct]',
                    options[j].correct
                  );
                  file.append(
                    'options' + '[' + j + '][position]',
                    options[j].position
                  );
                }
                file.append('languages', this.$root.$i18n.locale);

                var resCreate = addQuiz(file);
                resCreate.then((data) => {
                  if (data.message === 'Record successfully') {
                    this.dialogVisible = false;
                    this.$message({
                      message: this.$t('popup.quiz.notify.addSuccess'),
                      type: 'success',
                    });
                    this.listLoading = true;
                    this.ListQuiz = data.infor_page.data;
                    this.listQuery.page = data.infor_page.last_page;
                    this.total = data.infor_page.total;
                    this.last_page = data.infor_page.last_page;
                    this.listLoading = false;
                    this.dialogVisible = false;
                    this.isproces = false;
                  }
                });
              } else {
                this.$message({
                  message: this.$t('popup.quiz.notify.notAnswer'),
                  type: 'warning',
                });
              }
            } else {
              this.$message({
                message: this.$t('popup.quiz.notify.notOption'),
                type: 'warning',
              });
            }
          }

          if (this.tempAction.Action === 'Edit') {
            // check Edit
            var checkOptionEdit = false;
            for (var c = 0; c < this.ruleForm.options.length; c++) {
              if (
                this.ruleForm.options[c].option_ja === '' ||
                this.ruleForm.options[c].option_en === ''
              ) {
                checkOptionEdit = true;
                break;
              }
            }
            if (
              checkOptionEdit === false &&
              this.ruleForm.options.length >= 1
            ) {
              var countEdit = 0;
              for (var b = 0; b < this.ruleForm.options.length; b++) {
                if (this.ruleForm.options[b].correct === false) {
                  countEdit++;
                }
              }
              if (countEdit !== this.ruleForm.options.length) {
                this.processEditQuiz();
                this.isproces = true;
                var optionsEdit = this.ruleForm.options;
                for (var v = 0; v < optionsEdit.length; v++) {
                  optionsEdit[v].position = v + 1;
                }
                for (var y = 0; y < optionsEdit.length; y++) {
                  if (optionsEdit[y].correct === false) {
                    optionsEdit[y].correct = 0;
                  }
                  if (optionsEdit[y].correct === true) {
                    optionsEdit[y].correct = 1;
                  }
                }

                for (var k = 0; k < optionsEdit.length; k++) {
                  delete optionsEdit[k].quiz_id;
                }
                var fileEdit = new FormData();
                fileEdit.append('id', this.ruleForm.id);
                fileEdit.append('title_ja', this.ruleForm.title_ja);
                fileEdit.append('title_en', this.ruleForm.title_en);
                fileEdit.append('sentence_ja', this.ruleForm.sentence_ja);
                fileEdit.append('sentence_en', this.ruleForm.sentence_en);
                const file_img_ja = document.getElementById('pic_ja').files[0];
                const file_img_en = document.getElementById('pic_en').files[0];
                if (
                  this.img_ja !== this.domain + this.ruleForm.picture_ja &&
                  this.img_ja !== null &&
                  document.getElementById('pic_ja').files.length === 1
                ) {
                  fileEdit.append('picture_ja', file_img_ja);
                } else if (this.img_ja === null) {
                  fileEdit.append('updateIMG_ja', DELETE_IMAGE);
                }

                if (
                  this.img_en !== this.domain + this.ruleForm.picture_en &&
                  this.img_en !== null &&
                  document.getElementById('pic_en').files.length === 1
                ) {
                  fileEdit.append('picture_en', file_img_en);
                } else if (this.img_en === null) {
                  fileEdit.append('updateIMG_en', DELETE_IMAGE);
                }

                fileEdit.append(
                  'explain_correct_ja',
                  this.ruleForm.explain_correct_ja
                );
                fileEdit.append(
                  'explain_correct_en',
                  this.ruleForm.explain_correct_en
                );
                fileEdit.append(
                  'explain_incorrect_ja',
                  this.ruleForm.explain_incorrect_ja
                );
                fileEdit.append(
                  'explain_incorrect_en',
                  this.ruleForm.explain_incorrect_en
                );
                for (var u = 0; u < optionsEdit.length; u++) {
                  fileEdit.append(
                    'options' + '[' + u + '][option_ja]',
                    optionsEdit[u].option_ja
                  );
                  fileEdit.append(
                    'options' + '[' + u + '][option_en]',
                    optionsEdit[u].option_en
                  );
                  fileEdit.append(
                    'options' + '[' + u + '][correct]',
                    optionsEdit[u].correct
                  );
                  fileEdit.append(
                    'options' + '[' + u + '][position]',
                    optionsEdit[u].position
                  );
                  fileEdit.append(
                    'options' + '[' + u + '][status]',
                    optionsEdit[u].status
                  );
                  if (optionsEdit[u].status !== -2) {
                    fileEdit.append(
                      'options' + '[' + u + '][id]',
                      optionsEdit[u].id
                    );
                  }
                }
                fileEdit.append('languages', this.$root.$i18n.locale);
                fileEdit.append('current_page', this.listQuery.page);
                var resEditQuiz = editQuiz(fileEdit);

                resEditQuiz.then((data) => {
                  if (data.message === 'Edit successfully') {
                    this.dialogVisible = false;
                    this.$message({
                      message: this.$t('popup.quiz.notify.editSuccess'),
                      type: 'success',
                    });
                    this.listLoading = true;
                    this.ListQuiz = data.page_infor.data;
                    this.listQuery.page = data.page_infor.current_page;
                    this.total = data.page_infor.total;
                    this.last_page = data.page_infor.last_page;
                    this.listLoading = false;
                    this.isproces = false;
                  }
                });
              } else {
                this.$message({
                  message: this.$t('popup.quiz.notify.notAnswer'),
                  type: 'warning',
                });
              }
            } else {
              this.$message({
                message: this.$t('popup.quiz.notify.notOption'),
                type: 'warning',
              });
            }
          }
        } else {
          this.$message({
            message: this.$t('popup.quiz.notify.notInfor'),
            type: 'warning',
          });
        }
      });
    },
    insertRow() {
      // Insert Row for input Options
      this.ruleForm.options.push({
        option_ja: '',
        option_en: '',
        correct: false,
        position: this.ruleForm.options.length + 1,
        status: -2,
      });
    },
    closeDialog(formName) {
      this.dialogVisible = false;
      this.$refs[formName].resetFields();
      this.ruleForm = {
        title_ja: '',
        title_en: '',
        sentence_ja: '',
        sentence_en: '',
        options: [],
      };
      this.temp = [];
      this.arrDelete = [];
      this.tempPosition = [];
      document.getElementById('pic_ja').value = '';
      document.getElementById('pic_en').value = '';
      this.img_en = '';
      this.img_ja = '';
    },
    handleShow(row) {
      this.Title = 'Show quiz';
      var dataRow = Object.assign({}, row);
      this.tempAction.hidden = true;
      this.dialogVisible = true;
      const resShow = fetchData({
        id: dataRow.id,
      });
      var dataFake;
      resShow.then((data) => {
        dataFake = data;
        for (var i = 0; i < dataFake.options.length; i++) {
          if (dataFake.options[i].correct === 0) {
            dataFake.options[i].correct = false;
          } else {
            dataFake.options[i].correct = true;
          }
        }
        this.ruleForm = dataFake;
        if (dataFake.picture_en !== null) {
          this.img_en = this.domain + dataFake.picture_en;
        } else {
          this.img_en = dataFake.picture_en;
        }
        if (dataFake.picture_ja !== null) {
          this.img_ja = this.domain + dataFake.picture_ja;
        } else {
          this.img_ja = dataFake.picture_ja;
        }
      });
    },
    toggleCorrect(rows) {
      if (rows) {
        rows.forEach((row) => {
          this.$refs.dragTable.toggleRowSelection(row);
        });
      } else {
        this.$refs.dragTable.clearSelection();
      }
    },
    handletableCorrect(row) {
      if (this.tempAction.Action === 'Edit') {
        if (row.correct === true) {
          for (var b = 0; b < this.ruleForm.options.length; b++) {
            if (this.ruleForm.options[b].position === row.position) {
              if (row.status !== -2) {
                this.ruleForm.options[b].status = 1;
              }
            }
          }
        }

        if (row.correct === false) {
          for (var j = 0; j < this.ruleForm.options.length; j++) {
            if (this.ruleForm.options[j].position === row.position) {
              if (row.status !== -2) {
                this.ruleForm.options[j].status = 1;
              }
            }
          }
        }
      }
    },
    handleDeleteOneQuiz(row) {
      this.dialogVisibleDelete = true;
      this.tempAction.Action = 'DeleteOne';
      this.temp = row;
    },
    handleDeleteManyQuiz() {
      this.dialogVisibleDelete = true;
      this.tempAction.Action = 'DeleteMany';
    },
    // Delete One Quiz
    async DeleteOneQuiz(row) {
      this.sortTableChange();
      this.$message.closeAll();
      const Quiz = Object.assign({}, row);
      var current_page = this.listQuery.page;
      if (this.lengthData > 1) {
        current_page = this.listQuery.page;
      } else if (this.lengthData === 1) {
        current_page = this.listQuery.page - 1;
      }
      await deleteQuiz({
        id_list: [Quiz.id],
        current_page: current_page,
        languages: this.$root.$i18n.locale,
      })
        .then((res) => {
          this.$message({
            message: this.$t('popup.quiz.deleteSuccess'),
            type: 'success',
          });
          this.listLoading = true;
          this.ListQuiz = res.infor_page.data;
          this.total = res.infor_page.total;
          this.listQuery.last_page = res.infor_page.last_page;
          this.listQuery.page = res.infor_page.current_page;
          this.lengthData = res.infor_page.data.length;
          this.listLoading = false;
          this.listQuery.keyword = '';
          this.dialogVisibleDelete = false;
        })
        .catch((err) => {
          this.dialogVisibleDelete = false;
          if (err.response.status === 500 && err.response.data.error === 'errorPage.errorQuizInSurvey') {
            this.listIdDeleteFaild = err.response.data.delete_faild;
          }
        });
    },
    // Delete Many Quiz
    async DeleteManyQuiz() {
      this.sortTableChange();
      this.$message.closeAll();
      var dataDelete = Object.values(this.multipleSelection);
      if (this.multipleSelection.length === 0) {
        this.dialogVisibleDelete = false;
        this.$message({
          message: this.$t('popup.quiz.notify.notchoseDelete'),
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
        await deleteQuiz({
          id_list: dataDelete,
          current_page: current_page,
          languages: this.$root.$i18n.locale,
        })
          .then((res) => {
            this.$message({
              message: this.$t('popup.quiz.deleteSuccess'),
              type: 'success',
            });
            this.listLoading = true;
            this.ListQuiz = res.infor_page.data;
            this.total = res.infor_page.total;
            this.last_page = res.infor_page.last_page;
            this.listQuery.page = res.infor_page.current_page;
            this.lengthData = res.infor_page.data.length;
            this.listIdDeleteFaild = res.delete_faild;
            this.listQuery.keyword = '';
            this.listLoading = false;
            this.dialogVisibleDelete = false;
          })
          .catch((err) => {
            this.dialogVisibleDelete = false;
            if (err.response.status === 500 && err.response.data.error === 'errorPage.errorQuizInSurvey') {
              this.listIdDeleteFaild = err.response.data.delete_faild;
            }
          });
      }
    },
    getListIndexDeleteFaild() {
      var listIndex = [];

      for (var i = 0; i < this.ListQuiz.length; i++) {
        for (var j = 0; j < this.listIdDeleteFaild.length; j++) {
          if (this.ListQuiz[i].id === this.listIdDeleteFaild[j]) {
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
        '.el-table__body-wrapper > table > tbody'
      );
      // Get children in els => 10 tr
      els = els[0].children;
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
          '.el-table__body-wrapper > table > tbody'
        );
        // Get children in els => 10 tr
        els = els[0].children;
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
      var selectEl = this.$refs.tableQuiz.$el;
      var els = selectEl.querySelectorAll(
        '.el-table__body-wrapper > table > tbody'
      );
      // Get children in els => 10 tr
      els = els[0].children;
      // Loop to delete class "danger-row" in el
      for (var el = 0; el < els.length; el++) {
        // Check el has class "danger-row" ==> Pass to delete class "danger-roww"
        if (els[el].classList.contains('danger-row')) {
          els[el].classList.remove('danger-row');
        }
      }
    },
    submitConfrimDelete() {
      if (this.tempAction.Action === 'DeleteOne') {
        this.DeleteOneQuiz(this.temp);
      }
      if (this.tempAction.Action === 'DeleteMany') {
        this.DeleteManyQuiz();
      }
    },
    handleEdit(row) {
      this.tempAction.Action = 'Edit';
      this.Title = this.$t('popup.quiz.titleEdit');
      this.tempAction.hidden = false;
      this.dialogVisible = true;
      this.$nextTick(() => {
        this.setSort();
      });
      this.temp = row;
      const resShow = fetchData({
        id: this.temp.id,
      });
      var dataFake;
      resShow.then((data) => {
        dataFake = data;
        for (var i = 0; i < dataFake.options.length; i++) {
          dataFake.options[i].status = 0;
          if (dataFake.options[i].correct === 0) {
            dataFake.options[i].correct = false;
          } else {
            dataFake.options[i].correct = true;
          }
        }
        this.ruleForm = dataFake;
        if (dataFake.picture_en !== null) {
          this.img_en = this.domain + dataFake.picture_en;
        } else {
          this.img_en = dataFake.picture_en;
        }
        if (dataFake.picture_ja !== null) {
          this.img_ja = this.domain + dataFake.picture_ja;
        } else {
          this.img_ja = dataFake.picture_ja;
        }

        if (this.tempAction.Action === 'Edit') {
          for (var j = 0; j < dataFake.options.length; j++) {
            this.tempPosition.push(dataFake.options[j].position);
          }
        }
      });
    },
    handleDeleteOption(index) {
      if (this.ruleForm.options[index].status === -2) {
        if (index > -1) {
          this.ruleForm.options.splice(index, 1);
        }
      } else {
        this.ruleForm.options[index].status = -1;
        this.arrDelete.push(this.ruleForm.options[index]);
        if (index > -1) {
          this.ruleForm.options.splice(index, 1);
        }
      }
    },
    textChange(index) {
      if (this.ruleForm.options[index].status !== -2) {
        this.ruleForm.options[index].status = 1;
      }
    },
    processEditQuiz() {
      for (var v = 0; v < this.tempPosition.length; v++) {
        for (var t = 0; t < this.arrDelete.length; t++) {
          if (this.tempPosition[v] === this.arrDelete[t].position) {
            this.tempPosition.splice(v, 1);
          }
        }
      }

      for (var x = 0; x < this.tempPosition.length; x++) {
        if (
          this.ruleForm.options[x].status !== -2 &&
          this.ruleForm.options[x].position !== this.tempPosition[x]
        ) {
          this.ruleForm.options[x].status = 1;
        }
      }

      for (var i = 0; i < this.ruleForm.options.length; i++) {
        this.ruleForm.options[i].position = i + 1;
      }

      if (this.arrDelete.length > 0) {
        for (var p = 0; p < this.ruleForm.options.length; p++) {
          if (this.ruleForm.options[p].status !== -2) {
            this.ruleForm.options[p].status = 1;
          }
        }
        for (var j = 0; j < this.arrDelete.length; j++) {
          this.ruleForm.options.push(this.arrDelete[j]);
        }
      }
    },
  },
};
</script>

<style>
.sortable-ghost {
  opacity: 0.8;
  color: #fff !important;
  background: #42b983 !important;
}
.el-table .danger-row {
  background: rgb(253, 226, 226);
  color: #F56C6C;
  transition-duration: 1s;
}
</style>

<style scoped lang="scss">
.inputSearch {
  margin-left: 10px;
  width: 200px;
}

.space {
  padding: 1px;
}

.demonstration {
  font-weight: 700;
}

.btn-insert {
  margin-top: 10px;
  margin-bottom: 10px;
}

.pagination {
  margin-top: 10px;
  text-align: right;
}

.icon-star {
  margin-right: 2px;
}
.drag-handler {
  width: 20px;
  height: 20px;
  cursor: pointer;
}
.show-d {
  margin-top: 15px;
}

.dialog-title {
  display: inline;
}

.left {
  float: left;
  height: 30px;
}

.right {
  float: right;
  height: 30px;
  margin-right: 50px;
  margin-top: 19px;
}

::v-deep h1 {
  margin: 10px !important;
}

::v-deep .el-form-item {
  margin: 0px !important;
}

::v-deep .el-dialog__close {
  margin-top: 16px;
  font-size: 23px;
  color: #000;
  font-weight: 700;
}
.image_form_quiz {
  padding: 12px;
}
.image_form_quiz img {
  height: 100%;
  width: 100%;
  max-height: 220px;
  max-width: 220px;
}
.image-preview-wrapper {
  text-align: center;
}
.image-preview-action i {
  font-size: 22px;
}
.hidden {
  display: none;
}
</style>

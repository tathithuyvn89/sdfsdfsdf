<template>
  <div class="app-container">
    <div class="filter-container">
      <el-row>
        <!-- <el-col :span="2">
          <el-select
            v-model="selectTypeView"
            style="width: 100%"
          >
            <el-option
              v-for="(item, index2) in typeView"
              :key="index2"
              :label="item.type_name"
              :value="item.type_id"
            />
          </el-select>
        </el-col> -->
        <!-- <el-col :span="1"><div class="space" /></el-col> -->
        <!-- <el-col :span="3">
          <el-select
            v-model="selectedRespondents"
            filterable
            clearable
            default-first-option
            style="width: 100%;"
            :placeholder="$t('dashboard.placeholder.chooseRespondent')"
            @change="handleSearch()"
          >
            <el-option
              v-for="(item,index1) in ListRespondent"
              :key="index1"
              :label="item.name"
              :value="item.id"
            />
          </el-select>
        </el-col> -->
        <!-- <el-col :span="1"><div class="space" /></el-col> -->
        <!-- <el-col :span="3">
          <el-select
            v-model="selectStatus"
            filterable
            clearable
            default-first-option
            style="width: 100%;"
            :placeholder="$t('dashboard.placeholder.status')"
            @change="handleSearch()"
          >
            <el-option
              v-for="(item, index3) in ListStatus"
              :key="index3"
              :label="item.name"
              :value="item.id"
            />
          </el-select>
        </el-col> -->
        <!-- <el-col :span="1"><div class="space" /></el-col> -->
        <el-col :span="3">
          <el-select
            v-model="selectedSurveys"
            filterable
            clearable
            default-first-option
            style="width: 100%; text-align: left"
            :placeholder="$t('dashboard.placeholder.chooseSurvey')"
            @change="handleSearch(selectedSurveys)"
            @clear="handelClear(selectedSurveys)"
          >
            <el-option
              v-for="(item, index2) in ListSurvey"
              :key="index2"
              :label="item.title"
              :value="item.id"
            />
          </el-select>
        </el-col>
        <el-col :span="1"><div class="space" /></el-col>
        <!-- <el-col :span="6">
          <el-date-picker
            v-model="selectedDate"
            type="daterange"
            range-separator="To"
            :start-placeholder="$t('dashboard.placeholder.startDate')"
            :end-placeholder="$t('dashboard.placeholder.endDate')"
            style="width: 100%;"
            format="dd-MM-yyyy"
            value-format="yyyy-MM-dd"
            @change="handleSearch()"
          />
        </el-col> -->
        <el-col :span="1"><div class="space" /></el-col>
        <el-col :span="3" style="float: right">
          <el-button type="primary" style="width: 100%" @click="handleDownload">
            {{ $t('dashboard.button.export') }}
          </el-button>
        </el-col>
      </el-row>
    </div>

    <el-table
      :data="ListResult"
      border
      fit
      stripe
      highlight-current-row
      style="width: 100%"
    >
      <el-table-column
        :label="$t('dashboard.table.survey_id')"
        width="180"
        align="center"
        min-width="200"
      >
        <template slot-scope="scope">
          <span>{{ scope.row.survey_id }}</span>
        </template>
      </el-table-column>
      <el-table-column
        :label="$t('dashboard.table.survey_name')"
        width="180"
        align="center"
        min-width="200"
      >
        <template slot-scope="scope">
          <span>{{ scope.row.survey_name }}</span>
        </template>
      </el-table-column>
      <el-table-column
        :label="$t('dashboard.table.respondent_count')"
        align="center"
        min-width="200"
      >
        <template slot-scope="scope">
          <span>{{ scope.row.respondent_count }}</span>
        </template>
      </el-table-column>
      <el-table-column
        :label="$t('dashboard.table.complete_respondent_count')"
        align="center"
        min-width="200"
      >
        <template slot-scope="scope">
          <span>{{ scope.row.complete_respondent_count }}</span>
        </template>
      </el-table-column>
      <el-table-column
        :label="$t('dashboard.table.completed_rate')"
        align="center"
        min-width="200"
      >
        <template slot-scope="scope">
          <span>{{ scope.row.completed_rate }}%</span>
        </template>
      </el-table-column>
      <el-table-column
        :label="$t('dashboard.table.completed_count')"
        align="center"
        min-width="200"
      >
        <template slot-scope="scope">
          <span>{{ scope.row.completed_count }}</span>
        </template>
      </el-table-column>
      <el-table-column
        :label="$t('dashboard.table.average_respondence_count')"
        align="center"
        min-width="200"
      >
        <template slot-scope="scope">
          <span>{{ scope.row.average_respondence_count }}</span>
        </template>
      </el-table-column>
      <el-table-column
        v-if="hidden != true"
        :label="$t('dashboard.table.detail')"
        align="center"
        min-width="200"
      >
        <template slot-scope="{ row }">
          <el-button type="primary" @click="handleOpen(row)">{{
            $t('dashboard.table.open')
          }}</el-button>
        </template>
      </el-table-column>
    </el-table>

    <el-dialog
      title="Detail"
      :visible.sync="dialogTableVisible"
      width="1100px"
      @close="closeDialog('quizz_select')"
    >
      <el-button
        type="primary"
        style="width: 100%"
        @click="handleDownloadDetail"
      >
        {{ $t('dashboard.button.export') }}
      </el-button>
      <el-table :data="SurveyDetail1">
        <el-table-column
          property="quiz_id"
          :label="$t('dashboard.table.quiz_id')"
          min-width="100"
          align="center"
        />
        <el-table-column
          property="quiz_title"
          :label="$t('dashboard.table.quiz_title')"
          min-width="100"
          align="center"
        />
        <el-table-column
          :label="$t('dashboard.table.correct_rate')"
          min-width="100"
          align="center"
        >
          <template slot-scope="scope">
            <span>{{ scope.row.correct_rate }}%</span>
          </template>
        </el-table-column>
        <el-table-column
          property="average_mistake_count"
          :label="$t('dashboard.table.average_mistake_count')"
          min-width="100"
          align="center"
        />
        <el-table-column
          property="frequent_incorrect_answers"
          :label="$t('dashboard.table.frequent_incorrect_answers')"
          min-width="120"
          align="center"
        />
      </el-table>
    </el-dialog>
    <!-- <el-pagination
      current-page.sync="3"
      :page-size="3"
      :total="3"
      background
      layout="total, prev, pager, next, jumper"
      class="pagination"
      @current-change="getList"
    /> -->
  </div>
</template>

<script>
import { getResult, getListSurvey, getSurveyDetail } from '@/api/dashboard';
export default {
  name: 'DashboardAdminn',
  components: {},
  data() {
    return {
      tableData: [],
      gridData: [],
      param: {
        la: this.$root.$store.getters.language,
      },
      idSurvey: [],
      dialogTableVisible: false,
      quizz_select: [],
      total: 0,
      ListResult: [],
      ListResultChanged: [],

      ListSurvey: [],
      hidden: false,
      selectedSurveys: [],
      SurveyDetail: [],
      SurveyDetail1: [],
      SurveyDetail1Changed: [],
      last_page: undefined,
      lengthData: undefined,
      survey_name_detail: '',
      ListResultCosnt: [],
    };
  },
  computed: {
    changeLang() {
      return this.$root.$store.getters.language;
    },
  },
  watch: {
    changeLang() {
      this.GetResult();
    },
  },
  created() {
    this.GetResult();
    this.GetListSurvey();
    this.$store.getters.sidebar.opened = false;
  },
  methods: {
    handleOpen(row) {
      this.dialogTableVisible = true;
      this.survey_name_detail = row.survey_name;
      this.GetSurveyDetail(row.survey_id);
    },
    closeDialog(from) {
      this.quizz_select.splice(0);
    },
    async GetResult() {
      var lang = this.$root.$store.getters.language;
      this.param.la = lang;
      var data = await getResult(this.param);
      this.ListResult = data;
      this.ListResultCosnt = data;
      for (var i = 0; i < data.length; i++) {
        this.idSurvey.push(data[i].survey_id);
      }
    },
    async GetListSurvey(survey_id) {
      var lang = this.$store.getters.language;
      var params = {
        la: lang,
        survey_id: survey_id,
      };
      this.ListSurvey = await getListSurvey(params);
    },
    async GetSurveyDetail(survey_id) {
      var lang = this.$store.getters.language;
      var params = {
        la: lang,
        survey_id: survey_id,
      };
      var data = await getSurveyDetail(params);
      this.SurveyDetail1 = data;
    },
    handleDownload() {
      import('@/vendor/Export2Excel').then((excel) => {
        var tHeader = [];
        if (this.$store.getters.language === 'en') {
          tHeader = [
            'Survey ID',
            'Survey Name',
            'Respondent Count',
            'Complete Respondent Count',
            'Completed Rate',
            'Completed Count',
            'Average Respndence Count',
          ];
        } else if (this.$store.getters.language === 'ja') {
          tHeader = [
            'サーベイID',
            'サーベイ名',
            '中断を含む回答者人数',
            '回答終了人数',
            '回答終了者率',
            '回答終了回数',
            '一人当たりの回答終了回数の平均',
          ];
        }
        const filterVal = [
          'survey_id',
          'survey_name',
          'respondent_count',
          'complete_respondent_count',
          'completed_rate',
          'completed_count',
          'average_respondence_count',
        ];

        // Function export percentage from 0->100% to 0->1
        this.ListResultChanged = this.ListResult.map((item) => {
          return { ...item };
        });
        this.ListResultChanged.map((item) => {
          item.completed_rate = item.completed_rate / 100;
        });

        const data = this.formatJson(filterVal, this.ListResultChanged);
        excel.export_json_to_excel({
          header: tHeader,
          data,
          filename: 'ListResult',
          autoWidth: true,
          bookType: 'xlsx',
        });
      });
    },
    handleDownloadDetail() {
      import('@/vendor/Export2Excel').then((excel) => {
        var tHeader = [];
        if (this.$store.getters.language === 'en') {
          tHeader = [
            'Quizz ID',
            'Quizz Title',
            'Correct Rate',
            'Average Mistake Count',
            'Frequent Incorrect Answers',
          ];
        } else if (this.$store.getters.language === 'ja') {
          tHeader = [
            'クイズID',
            'クイズタイトル',
            '正解率',
            '平均ミス回数',
            'よく間違われる回答番号',
          ];
        }
        const filterVal = [
          'quiz_id',
          'quiz_title',
          'correct_rate',
          'average_mistake_count',
          'frequent_incorrect_answers',
        ];

        // Function export percentage detail from 0->100% to 0->1
        this.SurveyDetail1Changed = this.SurveyDetail1.map((item) => {
          return { ...item };
        });
        this.SurveyDetail1Changed.map((item) => {
          item.correct_rate = item.correct_rate / 100;
        });

        const data = this.formatJson(filterVal, this.SurveyDetail1Changed);
        excel.export_json_to_excel({
          header: tHeader,
          data,
          filename: this.survey_name_detail,
          autoWidth: true,
          bookType: 'xlsx',
        });
      });
    },
    formatJson(filterVal, jsonData) {
      return jsonData.map((v) =>
        filterVal.map((j) => {
          return v[j];
        })
      );
    },
    handleSearch(value) {
      this.ListResult = this.ListResultCosnt.filter(
        (data) => !value || data.survey_id === value
      );
    },
    handelClear(value) {
      this.ListResult = this.ListResultCosnt;
    },
  },
};
</script>

<style rel="stylesheet/scss" lang="scss" scoped>
.space {
  padding: 5px;
}
::v-deep .el-table th > .cell {
  word-break: break-word !important;
}
</style>

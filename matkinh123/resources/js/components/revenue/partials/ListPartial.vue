<template>
  <div>
      <h2 class="h2 mt-5 text-center">DANH SÁCH DOANH THU</h2>
    <!-- FORM FILTER -->
    <b-row class="mt-3 mb-5">
      <b-col md="6" class="offset-md-6 mb-4 text-right">
        <b-button-group>
          <b-button v-for="btn in buttons" v-on:click="btnFilterStatus(btn.id)" :pressed.sync="btn.state"
                    :variant="btn.variant" :key="btn.variant">
            {{ btn.caption }}
          </b-button>
        </b-button-group>
      </b-col>

      <b-col md="5" class="my-1 offset-md-7 mb-4 text-right">
        <b-form-group horizontal label="Số dòng trên trang" class="mb-0">
          <b-form-select :options="pageOptions" v-model="perPage"/>
        </b-form-group>
      </b-col>

      <b-col md="6" class="my-1">
        <b-form-group horizontal label="Tìm kiếm" class="mb-0">
          <b-input-group>
            <b-form-input v-model="filter" placeholder="Gõ tìm kiếm"/>
            <b-input-group-append>
              <b-btn :disabled="!filter" @click="filter = ''">Xoá</b-btn>
            </b-input-group-append>
          </b-input-group>
        </b-form-group>
      </b-col>

      <b-col md="6" class="my-1">
        <b-form-group horizontal label="Loại doanh thu" class="mb-0">
          <!-- <b-form-select :options="lstName" v-model="filter"/> -->
          <select v-model="filter" class="form-control" tabindex="">
            <option value=""></option>
            <option v-for="(name, index) in lstName" :key="index" :value="name">{{ name }}</option>
          </select>
        </b-form-group>
      </b-col>

      <b-col md="6" class="my-1">
        <b-form-group horizontal label="Ngày bắt đầu" class="mb-0">
          <b-input-group>
            <date-picker v-model="date_start" :config="options" class="date" placeholder="* Chọn Ngày Bắt Đầu"></date-picker>
            <div class="input-group-prepend">
              <span class="input-group-text" id="basic-addon1"><span data-feather="calendar"></span></span>
            </div>
          </b-input-group>
        </b-form-group>
      </b-col>

      <b-col md="6" class="my-1">
        <b-form-group horizontal label="Ngày kết thúc" class="mb-0">
          <b-input-group>
            <date-picker v-model="date_end" :config="options" class="date" placeholder="* Chọn Ngày Kết Thúc"></date-picker>
            <div class="input-group-prepend">
              <span class="input-group-text" id="basic-addon1"><span data-feather="calendar"></span></span>
            </div>
          </b-input-group>
        </b-form-group>
      </b-col>

      <!-- <b-col md="4" class="my-1">
        <b-form-group horizontal label="Sắp xếp" class="mb-0">
          <b-input-group>
            <b-form-select v-model="sortBy" :options="sortOptions">
              <option slot="first" :value="null">-- none --</option>
            </b-form-select>
            <b-form-select :disabled="!sortBy" v-model="sortDesc" slot="append">
              <option :value="false">Asc</option>
              <option :value="true">Desc</option>
            </b-form-select>
          </b-input-group>
        </b-form-group>
      </b-col>

      <b-col md="4" class="my-1">
        <b-form-group horizontal label="Hướng sắp xếp" class="mb-0">
          <b-input-group>
            <b-form-select v-model="sortDirection" slot="append">
              <option value="asc">Asc</option>
              <option value="desc">Desc</option>
              <option value="last">Last</option>
            </b-form-select>
          </b-input-group>
        </b-form-group>
      </b-col> -->

    </b-row>

    <!-- ALERTS -->
    <b-alert :show="alerts.dismissCountDown"
             dismissible
             :variant="alerts.status"
             @dismissed="alerts.dismissCountDown=0"
             @dismiss-count-down="countDownChanged">
      <p>{{alerts.msg}} {{alerts.dismissCountDown}} giây...</p>
      <b-progress :variant="alerts.status"
                  :max="alerts.dismissSecs"
                  :value="alerts.dismissCountDown"
                  height="4px">
      </b-progress>
    </b-alert>

    <!-- MAIN TABLE ELEMENT -->
    <b-table class="tbl-list" striped bordered outlined small hover foot-clone responsive="true"
              show-empty
              stacked="md"
              :items="items"
              :fields="fields"
              :current-page="currentPage"
              :per-page="perPage"
              :filter="filter"
              :sort-by.sync="sortBy"
              :sort-desc.sync="sortDesc"
              :sort-direction="sortDirection"
              @filtered="onFiltered">

      <!-- HEADER -->
      <template slot="HEAD_chk" slot-scope="row">
        <b-form-checkbox @click.native.stop v-model="chk_all"></b-form-checkbox>
      </template>

      <!-- BODY -->
      <!-- Rows -->
      <template slot="chk" slot-scope="row">
        <b-form-checkbox @click.native.stop name="chk" :value="row.item.id" class="mr-0"></b-form-checkbox>
      </template>
      <template slot="status" slot-scope="row">
        <p v-if="row.value === 0" class="font-weight-bold text-danger"><i class="fas fa-exclamation-circle"></i> Chưa
          thanh toán</p>
        <p v-else-if="row.value === 1" class="text-success"><i class="fas fa-check-circle"></i> Đã thanh toán</p>
        <p v-else class="text-warning"><i class="fas fa-angry"></i> Khách nợ</p>
      </template>
      <template slot="money" slot-scope="row"><p class="font-weight-bold text-primary">{{ row.value | money }}</p></template>
      <template slot="prepay" slot-scope="row"><p class="text-warning">{{ row.value | money }}</p></template>
      <template slot="money_lacks" slot-scope="row"><p class="font-weight-bold text-danger">{{ (row.item.money -
        row.item.prepay) | money }}</p></template>
      <template slot="created_at" slot-scope="row"><p class="">{{ row.value | datetime }}</p></template>

      <template slot="actions" slot-scope="row">
        <!-- We use @click.stop here to prevent a 'row-clicked' event from also happening -->
        <b-button size="sm" @click.stop="btnEditRow(row.item, row.index, $event.target)" class="mr-1">
          Chỉnh sửa
        </b-button>
        <b-button size="sm" @click.stop="row.toggleDetails">
          {{ row.detailsShowing ? 'Ẩn' : 'Hiện' }} chi tiết
        </b-button>
        <b-button size="sm" @click.stop="btnDelRow(row.item, row.index, $event.target)">
          Xoá
        </b-button>
      </template>

      <!-- Details Row -->
      <template slot="row-details" slot-scope="row">
        <b-card>
          <ul>
            <li v-for="(value, key) in row.item" :key="key">
              <pre><span class="font-weight-bold text-danger">{{ key }}</span>: {{ value}}</pre>
            </li>
          </ul>
        </b-card>
      </template>

    </b-table>

    <b-row>
      <b-col md="6" class="my-1">
        <b-pagination :total-rows="totalRows" :per-page="perPage" v-model="currentPage" class="my-0"/>
      </b-col>
    </b-row>

    <!-- Info modal -->
    <b-modal id="modalInfo" @hide="resetModal" :title="modalInfo.title" ok-only>
      <pre>{{ modalInfo.content }}</pre>
    </b-modal>
  </div>
</template>

<script>

  import moment from 'moment';

  export default {
    name: "ListPartial",
    components: {

    },

    props: {
      data: {
        type: Array,
        required: true
      },
      fields: {
        type: Array,
        required: true
      },
      buttons: {
        type: Array,
        required: true
      },
      alerts: Object,
    },

    data: function () {
      return {

        lstName: [
          "Doanh thu bán hàng",
          "Doanh thu hụi",
          "Lãi ngân hàng"
        ],

        // Datetimepicker
        options: {
          format: 'DD/MM/YYYY HH:mm:ss',
          useCurrent: true,
          showClear: true,
          showClose: true,
        },
        date_start: moment().startOf('year').format('DD/MM/YYYY HH:mm:ss'), // Ngày bắt đầu filter
        date_end: moment().endOf('year').format('DD/MM/YYYY HH:mm:ss'), // Ngày kết thúc filter

        // Items data
        items: [],
        chk_all: '',

        // Pagination and filters
        currentPage: 1,
        perPage: 20,
        totalRows: (this.items) ? this.items.length : 0,
        pageOptions: [10, 20, 50, -1],
        sortBy: null,
        sortDesc: false,
        sortDirection: 'asc',
        filter: null,
        modalInfo: {title: '', content: ''},
      }
    },

    watch: { // Auto listening data change
      data: function (items) {
        this.items = items;
      },

      date_start: function () {
        this.getDataWithDate();
      },
      date_end: function () {
        this.getDataWithDate();
      },
    },
    mounted() {
      // Contructor when connect first time
      this.getDataWithDate();
    },

    computed: {
      // Datatables sort
      sortOptions() {
        // Create an options list from our fields
        return this.fields
          .filter(f => f.sortable)
          .map(f => {
            return {text: f.label, value: f.key}
          })
      },
    },

    methods: {
      /* Button Event */

      // Get row with date
      getDataWithDate() {
        let date_start = moment(this.date_start, 'DD/MM/YYYY HH:mm:ss').format('YYYY-MM-DD HH:mm:ss');
        let date_end   = moment(this.date_end, 'DD/MM/YYYY HH:mm:ss').format('YYYY-MM-DD HH:mm:ss');
        let params     = {date_start: date_start, date_end: date_end};
        // this.$parent.$options.methods.getData(params);
        this.$parent.getData(params);
      },

      // Edit row
      btnEditRow(item, index, button) {

        // Scroll trình duyệt lên đầu trang
        window.scrollTo({
          top: 0,
          behavior: "smooth"
        });

        this.$parent.$refs.FormPartialRef.form = {
          c_id: item.id,
          type: item.type,
          name: item.name,
          money: String(item.money),
          prepay: String(item.prepay),
          status: item.status,
          created_at: moment(item.created_at).format("DD/MM/YYYY HH:mm:ss"),
          note: item.note,
          monthlyrevenue: item.monthlyrevenue,
        };

        // switch labels
        this.$parent.edit = true;

        // // Create popup modal
        // this.modalInfo.title = `Row index: ${index}`;
        // this.modalInfo.content = JSON.stringify(item, null, 2);
        // this.$root.$emit('bv::show::modal', 'modalInfo', button);
      },

      // Delete row
      btnDelRow(item, index, button) {
        if (confirm('Tôi đồng ý xoá dòng này!')) {
          this.$parent.delRow(item.id, index);
        }
      },

      // Filter row with status
      btnFilterStatus: function (id) {
        this.items = this.data;
        if (id !== -1) {
          this.items = this.items.filter(item => item.status === id);
        }
      },

      // Alerts
      showAlert(msg = '', status = '') {
        if (status) {
          this.alerts.status = status;
        }
        if (msg) {
          this.alerts.msg = msg;
        }
        this.alerts.dismissCountDown = this.alerts.dismissSecs;
      },
      countDownChanged(dismissCountDown) {
        this.alerts.dismissCountDown = dismissCountDown
      },

      // Modal
      resetModal() {
        this.modalInfo.title   = '';
        this.modalInfo.content = '';
      },
      onFiltered(filteredItems) {
        // Trigger pagination to update the number of buttons/pages due to filtering
        this.totalRows   = filteredItems.length;
        this.currentPage = 1;
      },

      // Date formatter
      customDateFormatter(date) {
        return moment(date).format('DD/MM/YYYY HH:mm:ss');
      }
    },

    filters: {}
  }
</script>

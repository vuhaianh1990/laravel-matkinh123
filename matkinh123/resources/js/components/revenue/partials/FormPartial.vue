<template>

  <div class="row">
    <div class="col-md-12">

      <!-- ALERTS -->
      <b-alert :show="alerts.dismissCountDown"
              dismissible
              :variant="alerts.status"
              @dismissed="alerts.dismissCountDown=0"
              @dismiss-count-down="countDownChanged">
        <div v-html="alerts.msg">{{alerts.dismissCountDown}} giây...</div>
        <b-progress :variant="alerts.status"
                    :max="alerts.dismissSecs"
                    :value="alerts.dismissCountDown"
                    height="4px">
        </b-progress>
      </b-alert>

      <div class="form-row">
        <div class="form-group col-md-3">
          <label for="type">Tên loại doanh thu</label>
          <div class="input-group mb-2">
            <div class="input-group-prepend">
              <label class="input-group-text" for="type">Loại</label>
            </div>
            <select v-model="form.type" class="form-control" id="type" tabindex="1">
              <option v-for="(type, index) in lstName" :key="index" :value="type">{{ type }}</option>
            </select>
          </div>
        </div>
        <div class="form-group col-md-6">
          <label for="name">Tên doanh thu</label>
          <div class="input-group mb-3">
            <div class="input-group-prepend">
              <span class="btn btn-outline-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Gợi ý</span>
              <div class="dropdown-menu">
                <button v-for="(name, index) in lstName" :key="index" :value="name" v-on:click="setName" class="dropdown-item" tabindex="2">{{name}}</button>
              </div>
            </div>
            <input v-model="form.name" type="text" class="form-control" placeholder="* Điền tên loại doanh thu" tabindex="3">
            <input type="hidden" v-model="form.type">
          </div>
        </div>
        <div class="form-group col-md-3">
          <label for="money">Tiền</label>
          <div class="input-group mb-2">
            <div class="input-group-prepend">
              <span class="input-group-text" id="basic-addon1"><span data-feather="dollar-sign"></span></span>
            </div>
            <input-number v-model="form.money" id="money" name="money" placeholder="* Điền giá trị tiền" tabindex="4"></input-number>
          </div>
        </div>
        <div class="form-group col-md-4">
          <label for="prepay">Tiền trả trước</label>
          <div class="input-group mb-2">
            <div class="input-group-prepend">
              <span class="btn btn-outline-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">{{ form.status ? lstStatus[form.status] : lstStatus[1]}}</span>
              <div class="dropdown-menu">
                <button v-for="(status, index) in lstStatus" :key="index" :value="index" v-on:click="setStatus" class="dropdown-item" tabindex="5">{{status}}</button>
              </div>
            </div>
            <input-number v-model="form.prepay" id="prepay" name="prepay" :disabled="disPrepay" placeholder="* Điền giá trị tiền" tabindex="6"></input-number>
          </div>
        </div>
        <div class="form-group col-md-4">
          <label for="monthlyrevenue">Kết chuyển doanh thu hàng tháng</label>
          <div class="input-group mb-2">
            <div class="input-group-prepend">
              <label class="input-group-text" for="monthlyrevenue"><span data-feather="dollar-sign"></span></label>
            </div>
            <select v-model="form.monthlyrevenue" class="form-control" id="monthlyrevenue" tabindex="7">
              <option v-for="(monthlyrevenue, index) in lstMonthlyRevenue" :key="index" :value="index">{{ monthlyrevenue }}</option>
            </select>
          </div>
        </div>
        <div class="form-group col-md-4">
          <label for="created_at">Ngày tháng</label>
          <div class="input-group" role="group">
            <date-picker v-model="form.created_at" :config="options" class="date" placeholder="* Chọn Ngày" tabindex="8"></date-picker>
            <div class="input-group-prepend">
              <span class="input-group-text" id="basic-addon1"><span data-feather="calendar"></span></span>
            </div>
          </div>
        </div>

        <div class="form-group col-md-12">
          <label for="note">Chú thích</label>
          <textarea v-model="form.note" class="form-control" id="note" name="note" rows="3" placeholder="Điền thông tin chú thích" tabindex="9"></textarea>
        </div>

        <input type="hidden" v-model="form.c_id">

        <button class="btn btn-lg btn-primary mr-2" v-show="form.c_id == ''" v-on:click="onSubmit" tabindex="10">Thêm</button>
        <button class="btn btn-lg btn-warning mr-2" v-show="form.c_id != ''" v-on:click="editRow" tabindex="10">Sửa</button>
        <button class="btn btn-lg btn-danger" v-on:click="onReset" tabindex="11">Xoá</button>

      </div>
    </div>
  </div>

</template>

<script>
import moment from "moment";

export default {
  name: "FormPartial",

  components: {

  },

  props: {
    alerts: Object,
  },

  data: function() {
    return {

      disPrepay: true, // Flag Disabled prepay field

      // Datepicker
      options: {
        format: 'DD/MM/YYYY HH:mm:ss',
        useCurrent: true,
        showClear: true,
        showClose: true,
      },

      // Form
      form: {
        c_id: "",
        name: "",
        money: 0,
        prepay: 0,
        status: 1,
        type: 'Doanh thu bán hàng',
        created_at: moment(),
        note: "",
        monthlyrevenue: 0,
      },

      lstName: [
        "Doanh thu bán hàng",
        "Doanh thu hụi",
        "Lãi ngân hàng"
      ],
      lstStatus: [
        "Chưa thanh toán (Còn nợ)",
        "Đã thanh toán"
      ],
      lstMonthlyRevenue: [
        'Không', // Không phải doanh thu hàng tháng
        'Chu kì 1 Tháng',
        'Chu kì 2 Tháng',
        'Chu kì 3 Tháng',
        'Chu kì 4 Tháng',
        'Chu kì 5 Tháng',
        'Chu kì 6 Tháng',
        'Chu kì 7 Tháng',
        'Chu kì 8 Tháng',
        'Chu kì 9 Tháng',
        'Chu kì 10 Tháng',
        'Chu kì 11 Tháng',
        'Chu kì 1 Năm',
        'Chu kì 2 Năm',
        'Chu kì 3 Năm',
        'Kích hoạt chu kì vĩnh viễn', // Kích hoạt doanh thu hàng tháng (chu kì vĩnh viễn)
      ],
    };
  },

  watch: {
  },

  computed: {

  },

  methods: {
    onSubmit(evt) {

      // Check validate
      const valid = this.checkValidation();
      if (valid === false) {
        return false;
      }

      // Format date
      if (moment(this.form.created_at, 'DD/MM/YYYY HH:mm:ss').isValid()) {
        this.form.created_at = moment(this.form.created_at, 'DD/MM/YYYY HH:mm:ss').format("YYYY-MM-DD HH:mm:ss");
      }

      this.$parent.loading = true; // Open loading

      // Thêm sản phẩm
      axios.post('/api/revenues', {data: this.form, _method: 'PUT'})
        .then(res => {
          if (res.data.status === 200) {
            this.notificationAlert("success");
            this.showAlert("Thêm thành công", 'success');
            this.form = {
              c_id: "",
              name: "",
              money: 0,
              prepay: 0,
              status: 1,
              type: 'Doanh thu bán hàng',
              created_at: moment(),
              note: "",
              monthlyrevenue: 0,
            }
            this.$parent.getData();
          } else {
            this.notificationAlert("danger");
            const msg = `<p>${res.data.msg}:</p> <p>${JSON.stringify(res.data.valid)}<p>`;
            this.showAlert(msg, 'danger');
          }

          this.$parent.loading = false; // Close loading
        })
        .catch(error => {
          this.$parent.loading = false; // Close loading
          console.log(error)
        });

        // switch labels
        this.$parent.edit = false;
    },
    onReset(evt) {
      /* Reset our form values */
      this.form = {
        c_id: "",
        name: "",
        money: 0,
        prepay: 0,
        status: 1,
        type: 'Doanh thu bán hàng',
        created_at: moment(),
        note: "",
        monthlyrevenue: 0,
      }


    },

    editRow(evt) {

      // Check validate
      const valid = this.checkValidation();
      if (valid === false) {
        return false;
      }

      if (this.form.c_id == '') {
        showAlert('Không thể chỉnh sửa được tuỳ chọn này. Xin vui lòng chọn lại mục muốn sửa.');
      }

      if (!confirm('Bạn có chắc muốn sửa mục này không?')) {
        return false;
      }

      // Format date
      if (moment(this.form.created_at, 'DD/MM/YYYY HH:mm:ss').isValid()) {
        this.form.created_at = moment(this.form.created_at, 'DD/MM/YYYY HH:mm:ss').format("YYYY-MM-DD HH:mm:ss");
      }

      this.$parent.loading = true; // Open loading

      axios.post('/api/revenues/' + this.form.c_id, {data: this.form, _method: 'PUT'})
        .then(res => {
          if (res.data.status === 200) {

            this.notificationAlert("success");
            this.showAlert("Thêm thành công", 'success');
            this.form = {
              c_id: "",
              name: "",
              money: 0,
              prepay: 0,
              status: 1,
              type: 'Doanh thu bán hàng',
              created_at: moment(),
              note: "",
              monthlyrevenue: 0,
            }
            this.$parent.getData();


          } else {
            this.notificationAlert("danger");
            const msg = `Không thể chỉnh sửa được tuỳ chọn này. Xin vui lòng chọn lại mục muốn sửa. <br><p>${res.data.msg}:</p> <p>${JSON.stringify(res.data.valid)}<p>`;
            this.showAlert(msg, 'danger');
            showAlert('');
          }

          this.$parent.loading = false; // Close loading
        })
        .catch(error => {
          this.$parent.loading = false; // Close loading
          console.log(error)
        });

        // switch labels
        this.$parent.edit = false;
    },

    // Event set hint name to input name
    setName: function(event) {
      this.form.name = event.target.value;
    },

    // Function calculator total money
    totalMoney: function() {
      if (!this.data) return "0";
      return this.data
        .map(item => parseInt(item.money))
        .reduce((prev, next) => prev + next);
    },


    setStatus(event) {
      this.form.status = event.target.value;

      if (this.form.status == 1) {
        this.disPrepay = true;
      } else {
        this.disPrepay = false;
      }
    },

    checkValidation() {
      let alert = [];
      if (this.form.name == '') alert.push('name');
      if (this.form.money === '') alert.push('money');
      if (this.form.type === '') alert.push('type');
      if (this.form.status === '') alert.push('status');
      if (this.form.created_at == '') alert.push('created_at');

      if (this.form.status == 0 && (this.form.prepay == '' || this.form.prepay == 0)) {
        alert.push('prepay');
      }

      if (alert.length > 0)  {
        this.notificationAlert("danger");
        let msg = '';
        alert.map(function(val, index) {
          msg += `<p>Xin vui lòng nhập giá trị ` + val + ` vào field dưới.</p>`;
        });

        this.showAlert(msg, 'danger');
        return false;
      }

      // Check money < 1000
      if (this.form.money < 1000) {
        this.showAlert('Tiền không được dưới 1000 VND', 'danger');
        return false;
      }

      return true;
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

    // Alert notification
    notificationAlert: function(status) {
      if (!window.Notification) {
        alert("Trình duyệt không hỗ trợ Desktop Notification (cảnh báo) này!");
      } else if (Notification.permission === "default") {
        Notification.requestPermission(function(p) {
          if (p === "denied")
            alert(
              "Bạn đã tắt cảnh báo hỗ trợ Desktop Notification (cảnh báo) này!"
            );
          else {
            if (status == 'danger') {
              const notify = new Notification("Lỗi!", {
                body: "Xin vui lòng nhập tất cả \ncác trường cần thiết",
                icon: "https://vuejs.org/images/logo.png"
              });
            } else if (status == 'success') {
              const notify = new Notification("Thành công!", {
                body: "Thêm sản phẩm thành công",
                icon: "https://vuejs.org/images/logo.png"
              });
            }

          }
        });
      } else {
        if (status == 'danger') {
          const notify = new Notification("Lỗi!", {
            body: "Xin vui lòng nhập tất cả \ncác trường cần thiết",
            icon: "https://vuejs.org/images/logo.png"
          });
        } else if (status == 'success') {
          const notify = new Notification("Thành công!", {
            body: "Thêm sản phẩm thành công",
            icon: "https://vuejs.org/images/logo.png"
          });
        }
      }
    },
  },

  filters: {}
};
</script>

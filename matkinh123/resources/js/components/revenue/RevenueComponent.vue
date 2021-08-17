<template>
  <div>
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
      <h1 class="h1">Doanh Thu</h1>
      <h4><span v-bind:class="[edit ?  'badge-warning' : 'badge-primary']" class="badge">{{ edit ? 'Sửa' : 'Thêm' }}</span></h4>
    </div>
    <!-- FORM -->
    <FormPartial ref="FormPartialRef" :alerts="lstAlerts"></FormPartial>
    <hr class="mt-5 mb-5">
    <!-- LIST -->
    <ListPartial ref="ListPartialRef" :data="data" :fields="fields" :buttons="buttons" :alerts="lstAlerts"></ListPartial>

    <!-- Loading -->
    <div id="preloader" v-show="loading">
      <div id="loader"></div>
    </div>
  </div>
</template>

<script>

  import ListPartial from "./partials/ListPartial";
  import FormPartial from "./partials/FormPartial";

  export default {
    components: {
      FormPartial,
      ListPartial
    },

    data: function () {
      return {
        loading: false,
        edit: false, // Switch labels

        // Datatables
        data: [],
        fields: [
          {key: 'chk', label: 'chk_all', sortable: false, class: 'text-center'},
          {key: 'id', label: 'ID', sortable: true, sortDirection: 'desc'},
          {key: 'name', label: 'Tên', sortable: true},
          {key: 'money', label: 'Tiền', sortable: true},
          {key: 'prepay', label: 'Trả trước', sortable: true},
          {key: 'money_lacks', label: 'Số tiền KH thiếu'},
          {key: 'status', label: 'Trạng thái', sortable: true, 'class': 'text-center'},
          {key: 'created_at', label: 'Ngày tạo', sortable: true},
          {key: 'actions', label: 'Thao tác'}
        ],

        lstAlerts: {
          dismissSecs: 10,
          dismissCountDown: 0,
          status: 'success',
          msg: ''
        },

        // Button
        buttons: [
          {variant: 'primary', caption: 'Tất cả', state: false, id: -1},
          {variant: 'danger', caption: 'Chưa thanh toán', state: false, id: 0},
          {variant: 'warning', caption: 'Nợ hàng', state: false, id: 2},
          {variant: 'success', caption: 'Đã thanh toán', state: false, id: 1},
        ],

      };
    },

    mounted: function () {},

    computed: {},

    methods: {

      // Get list datatable
      getData: function (params = {}) {
        this.loading = true; // Open loading
        axios.post('/api/revenues', params)
          .then(res => {
            if (res.data.status === 200) {
              this.data = res.data['data'];
            } else {
              console.log(res.data.msg);
            }
            this.loading = false;
          })
          .catch(error => {
            this.loading = false;
            console.log(error)
          });
      },

      // Delete row in datatable
      delRow: function (id, index) {
        axios.post('/api/revenues', {id: id, _method: 'DELETE'})
          .then(res => {
            if (res.data.status === 400 || res.data.status === 404) {
              // Alert
              this.lstAlerts['status'] = 'danger';
              this.lstAlerts['msg'] = res.data.msg;
              this.$refs.ListPartialRef.showAlert();
              // Notification alert
              this.notificationAlert('danger');
              return;
            }

            // Delete row
            this.data.splice(index, 1);

            this.lstAlerts['status'] = 'success';
            this.lstAlerts['msg'] = res.data.msg;
            this.$refs.ListPartialRef.showAlert();
          })
          .catch(error => {
            this.notificationAlert('danger');
            console.log(error)
          });
      },

      // Alert notification
      notificationAlert: function (status) {
        if (!window.Notification) {
          alert("Trình duyệt không hỗ trợ Desktop Notification (cảnh báo) này!");
        } else if (Notification.permission === 'default') {
          Notification.requestPermission(function (p) {
            if (p === 'denied')
              alert('Bạn đã tắt cảnh báo hỗ trợ Desktop Notification (cảnh báo) này!');
            else {
              const notify = new Notification('Lỗi!', {
                body: 'Xin vui lòng nhập tất cả \ncác trường cần thiết',
                icon: 'https://vuejs.org/images/logo.png'
              });
            }
          });
        } else {
          const notify = new Notification('Lỗi 2!', {
            body: 'Xin vui lòng nhập tất cả \ncác trường cần thiết',
            icon: 'https://vuejs.org/images/logo.png'
          });
        }
      },
    },

    filters: {}
  };
</script>

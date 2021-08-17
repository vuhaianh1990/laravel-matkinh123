<template>

<div>

  <div class="form-group row">

    <!-- Alert -->
    <b-alert class="col-12" :show="dismissCountDown"
            dismissible
            variant="danger"
            @dismissed="dismissCountDown=0"
            @dismiss-count-down="countDownChanged">
      <strong>Lỗi!</strong> {{ msgAlert }} {{ dismissCountDown }}s
      <b-progress variant="warning"
                  :max="dismissSecs"
                  :value="dismissCountDown"
                  height="4px">
      </b-progress>
    </b-alert>

    <!-- FORM -->
    <label for="date" class="col-md-1 col-sm-12 col-form-label">Lọc dữ liệu</label>
    <div class="col-md-2 col-sm-12">
      <select v-model="date" id="date" class="form-control">
        <option v-for="(type, index) in lstDate" :key="index" :value="index">{{ type }}</option>
      </select>
    </div>
    <label for="filterDate" class="col-md-1 col-sm-12 col-form-label">Theo</label>
    <div class="col-md-2 col-sm-12">
      <select v-model="filterDate" id="filterDate" class="form-control">
        <option v-for="(type, index) in lstFilterDate" :key="index" :value="index">{{ type }}</option>
      </select>
    </div>
  </div>

  <div id="betweenDate" v-show="showBetweenDate" class="card card-body">
    <div class="row">
      <div class="col-md-6 col-sm-12">
        <label for="startDate">Chọn Ngày Bắt Đầu</label>
        <date-picker v-model="startDate" id="startDate" @dp-change="changeFilterDateAndBetweenDate" :config="options" placeholder="* Chọn Ngày Bắt Đầu"></date-picker>
      </div>

      <div class="col-md-6 col-sm-12">
        <label for="endDate">Chọn Ngày Kết Thúc</label>
        <date-picker v-model="endDate" id="endDate" @dp-change="changeFilterDateAndBetweenDate" :config="options" placeholder="* Chọn Ngày Kết Thúc"></date-picker>
      </div>
    </div>
  </div>

  <canvas class="my-4" id="myChart" width="900" height="380"></canvas>

  <!-- Loading -->
  <div id="preloader" v-show="loading">
    <div id="loader"></div>
  </div>

</div>

</template>

<script>

import moment from "moment";
import Chart from 'chart.js';

// Show tooltips always even the stats are zero (Plugin luon hien thi tooltip cho chart.js)
/*
Chart.pluginService.register({
    beforeRender: function(chart) {
        if (chart.config.options.showAllTooltips) {
            // create an array of tooltips
            // we can't use the chart tooltip because there is only one tooltip per chart
            chart.pluginTooltips = [];
            chart.config.data.datasets.forEach(function(dataset, i) {
                chart.getDatasetMeta(i).data.forEach(function(sector, j) {
                    chart.pluginTooltips.push(new Chart.Tooltip({
                        _chart: chart.chart,
                        _chartInstance: chart,
                        _data: chart.data,
                        _options: chart.options.tooltips,
                        _active: [sector]
                    }, chart));
                });
            });

            // turn off normal tooltips
            chart.options.tooltips.enabled = false;
        }
    },
    afterDraw: function(chart, easing) {
        if (chart.config.options.showAllTooltips) {
            // we don't want the permanent tooltips to animate, so don't do anything till the animation runs atleast once
            if (!chart.allTooltipsOnce) {
                if (easing !== 1)
                    return;
                chart.allTooltipsOnce = true;
            }

            // turn on tooltips
            chart.options.tooltips.enabled = true;
            Chart.helpers.each(chart.pluginTooltips, function(tooltip) {
                tooltip.initialize();
                tooltip.update();
                // we don't actually need this since we are not animating tooltips
                tooltip.pivot();
                tooltip.transition(easing).draw();
            });
            chart.options.tooltips.enabled = false;
        }
    }
});
*/


export default {
  components: {
  },
  data: function() {
    return {
      filterDate: 'day',
      date: 'week',
      startDate: '',
      endDate: moment().format('DD/MM/YYYY'),
      chart: Object,
      loading: false,
      showBetweenDate: false,

      lstFilterDate: {
        'day': 'Ngày',
        'week': 'Tuần',
        'month': 'Tháng',
        'year': 'Năm'
      },
      lstDate: {
        'week': 'Tuần',
        'month': 'Tháng',
        'year': 'Năm',
        'all': 'Tất cả',
        'custom': 'Tuỳ chọn'
      },
      options: {format: 'DD/MM/YYYY', useCurrent: true, showClear: true, showClose: true},

      // Alert
      dismissSecs: 10,
      dismissCountDown: 0,
      msgAlert: ''
    }
  },
  mounted: function () {

    const config = {
        type: 'bar',
        data: {
          labels: [],
          datasets: []
        },
        options: {
            showAllTooltips: true,
            responsive: true,
            scales: {
                yAxes: [{
                    stacked: true,
                    ticks: {
                      beginAtZero: true,
                      // Include a dollar sign in the ticks
                      callback: function(money, index, values) {
                        const currency_format = money.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                        return currency_format + ' VND';
                      }
                    }
                }],
                xAxes: [{
                  stacked: true,
                }],
            },
            legend: {
                // display: false
            },
            title: {
                display: true,
                text: 'Tổng Doanh Thu Bán Hàng'
            },
            tooltips: {
              mode: 'index',
              intersect: false,
              callbacks: {
                label: function(tooltipItem, chart) {
                  const money = tooltipItem.yLabel;
                  const currency_format = money.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") + ' VND';
                  const datasetLabel    = chart.datasets[tooltipItem.datasetIndex].label || '';
                  return datasetLabel + ': ' + currency_format;
                }
              }
            },
        }
    }

    // create chart
    const ctx  = document.getElementById('myChart');
    this.chart = new Chart(ctx, config);


    // Initialize call function getData statistics
    this.getData();


  },
  watch: {
    filterDate: function() {
      this.changeFilterDateAndBetweenDate();
    },
    date: function() {
      this.changeDate();
    }
  },
  methods: {

    getData(date = 'week', filterDate = 'day', betweenDate = {}) {
      // Check validate
      var valid = this.checkValidation(date, betweenDate, filterDate);
      if (valid === false) {
        this.showAlert('Giá trị ngày lọc bắt đầu không được nhỏ hơn giá trị kết thúc.')
        return false;
      }

      // Loading
      this.loading = true;

      axios.post('/api/statistics', {
        filterDate: filterDate,
        date: date,
        betweenDate: betweenDate
      })
      .then(res => {
        // Reset data
        this.chart.reset();

        // Check validate
        if (res.data.status === 200) {
          this.chart.data = res.data.data;
        } else { // If data empty
          this.chart.data = {
            labels: [],
            datasets: []
          };

          this.showAlert(res.data.msg);
          console.log(res.msg);
        }

        // loading
        this.loading = false;
        // Load again data for chartjs
        this.chart.update();
      }, (error)  =>  {
        // loading
        this.loading = false;
      });

    },

    // Format Currency
    formatCurrency (currency) {
      if (!currency && currency !== 0) return '';
      const currency_format = currency.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
      return currency_format + ' VND';
    },

    // Check validate
    checkValidation(date, betweenDate, filterDate) {
      switch (filterDate) {
        case 'week':
          if (date === 'week') {
            return false;
          }
          break;
        case 'month':
          if (date === 'week' || date === 'month') {
            return false;
          }
          break;
        case 'year':
          if (date === 'week' || date === 'month' || date === 'year') {
            return false;
          }
          break;
      }

      if (date === 'custom') {
        startDate = new Date(betweenDate.startDate);
        endDate   = new Date(betweenDate.endDate);
        if (startDate > endDate) {
          return false;
        }
      }
    },

    changeDate() {
      this.showBetweenDate = false;
      if (this.date === 'custom') {
        this.showBetweenDate = true;
        return; // Trường hợp muốn trả về return false khi date == custom
      }
      this.getData(this.date, this.filterDate);
    },

    changeFilterDateAndBetweenDate() {
      const betweenDate = {
        'startDate': this.startDate,
        'endDate': this.endDate
      }

      this.getData(this.date, this.filterDate, betweenDate);
    },


    // Alert
    countDownChanged (dismissCountDown) {
      this.dismissCountDown = dismissCountDown
    },
    showAlert (msg = '') {
      this.msgAlert         = msg;
      this.dismissCountDown = this.dismissSecs
    }
  }
}
</script>

import Vue from "vue"
import moment from "moment";

// Format date
Vue.filter('date', function (date) {
  if (!date) return '...';
  return moment(date).format('DD/MM/YYYY')
});

// Format datetime
Vue.filter('datetime', function (date) {
  if (!date) return '...';
  return moment(date).format('DD/MM/YYYY HH:mm:ss')
});

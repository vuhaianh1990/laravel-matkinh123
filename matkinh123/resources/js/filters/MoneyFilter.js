import Vue from "vue"

// Format number currency
Vue.filter('money', function (currency) {
  if (!currency) return `0 VND`;
  const currency_format = currency.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
  return currency_format + ' VND';
});

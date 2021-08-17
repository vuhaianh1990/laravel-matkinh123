<?php

namespace Matkinh123\Controllers\Admin;

use Illuminate\Http\Request;
use Matkinh123\Controllers\Controller;
use Matkinh123\Models\Revenue;
use Matkinh123\Models\Cost;
use DB;
use Lang;

class StatisticsController extends Controller
{
     /**
     * API return Statistics
     *
     * @param Request $request
     */
    public function getStatistics(Request $request)
    {
        $validatedData = $request->validate([
            'filterDate'  => 'required',
            'date'        => 'required',
        ]);

        $filterDate  = $request->filterDate;
        $date        = $request->date;
        $betweenDate = $request->betweenDate;
        $startDate   = '';
        $endDate     = '';

        switch ($date) {
            case 'week':
                list($startDate, $endDate) = getStartAndEndDateWeek(); // Function defined in app/Http/helpers.php
                break;
            case 'month':
                list($startDate, $endDate) = getStartAndEndDateMonth(); // Function defined in app/Http/helpers.php
                break;
            case 'year':
                list($startDate, $endDate) = getStartAndEndDateYear(); // Function defined in app/Http/helpers.php
                break;
            case 'custom':
                // Check validate
                if (empty($betweenDate['startDate'])) {
                    return response()->json([
                        'status' => 404,
                        'msg'    => 'Xin chọn ngày bắt đầu'
                    ]);
                }
                $dateTime     = \DateTime::createFromFormat('d/m/Y', $betweenDate['startDate']);
                $startDate    = $dateTime->format('Y-m-d');
                $dateTime     = \DateTime::createFromFormat('d/m/Y', $betweenDate['endDate']);
                $endDate      = $dateTime->format('Y-m-d');
                break;
            default:
                list($startDate, $endDate) = $this->getStartAndEndDateWeek();
                break;
        }


        $revenues = $this->getDataService(new Revenue(), $date, $filterDate, $startDate, $endDate);
        $costs    = $this->getDataService(new Cost(), $date, $filterDate, $startDate, $endDate);

        // Check validate
        if ($revenues->count() <= 0 && $costs->count() <= 0) {
            return response()->json([
                'status' => 404,
                'msg'    => 'Khong ton tai du lieu'
            ]);
        }

        $result = [
            'revenues' => $revenues,
            'costs'    => $costs,
        ];

        $datasets = $this->getDataSetForChart($result, $filterDate, $date, $startDate, $endDate);

        return response()->json([
            'status' => 200,
            'data'   => $datasets
        ]);
    }

    /**
     *
     */
    private function getDataService($model, $date = 'week', $filterDate = 'day', $startDate = '', $endDate = '')
    {
        // SQL Query
        switch ($filterDate) {
            case 'day':
                $format_date = "DATE_FORMAT(created_at, '%Y-%m-%d')";
                break;

            case 'week':
                $format_date  = 'WEEK(created_at)';
                // $format_date .= '(curdate() - INTERVAL((WEEKDAY(curdate()))+1) DAY) first_day_of_week, ';
                // $format_date .= '(curdate() - INTERVAL((WEEKDAY(curdate()))+7) DAY) last_day_of_week';
                break;

            case 'month':
                $format_date = "DATE_FORMAT(created_at, '%Y-%m')";
                break;

            case 'year':
                $format_date = "DATE_FORMAT(created_at, '%Y')";
                break;
        }

        /**
         * Check tinh trang status da thanh toan chua
         * IF(COUNT(status) = SUM(status), 1, 0) status
         */
        $query = $model->select(
            DB::raw("
                $format_date date,
                SUM(money) moneys,
                SUM(prepay) prepay,
                IF(COUNT(status) = SUM(status), 1, 0) status
            ")
        )
        ->groupBy( "date" )
        ->orderBy( "date" );

        if ($date !== 'all') {
            $query = $query->whereBetween('created_at', ["$startDate 00:00:00", "$endDate 23:59:59"]);
        }

        return $query->get();
    }

    private function getDataSetForChart($result, $filterDate, $date, $startDate, $endDate)
    {
        $revenues      = [];                    // Doanh thu
        $cus_debts     = [];                    // Khách nợ
        $costs         = [];                    // Chi phí
        $cost_debts    = [];                    // Nợ hàng hoá
        $profit        = [];                    // Lợi  nhuận
        $labels        = [];                    // Labels contain array dates for Chartjs
        $rsRevenues    = $result['revenues'];
        $rsCosts       = $result['costs'];
        $countRevenues = $rsRevenues->count();
        $countCosts    = $rsCosts->count();

        // Trường hợp $date == 'all' && filterDate = 'day'
        if ($date === 'all' && $filterDate === 'day') {
            if ($countCosts <= 0) {
                $startDate         = $rsRevenues[0]->date;
                $endDate           = $rsRevenues[($countRevenues - 1)]->date;
            } else if ($countRevenues <= 0) {
                $startDate         = $rsCosts[0]->date;
                $endDate           = $rsCosts[($countCosts - 1)]->date;
            } else {
                $startDateRevenues = $rsRevenues[0]->date;
                $endDateRevenues   = $rsRevenues[($countRevenues - 1)]->date;
                $startDateCosts    = $rsCosts[0]->date;
                $endDateCosts      = $rsCosts[($countCosts - 1)]->date;

                // startDate and endDate for 'all'
                $startDate         = ($startDateRevenues >= $startDateRevenues) ? $startDateRevenues : $startDateCosts;
                $endDate           = ($endDateRevenues >= $endDateCosts) ? $endDateRevenues : $endDateCosts;
            }
        }

        if ($filterDate === 'day') {
            /**
             * Vì khi lấy data từ db sẽ có thể có ngày bị lủng
             * nên ta so sánh ngày lấy từ db và khoảng cách ngày bắt đầu và kết thúc
             * bằng nhau hay không. Mục đích để chèn những ngày bị lủng trong tuần, tháng, năm
             * hiển thị lên sơ đồ với value = 0
             */

            //  Get list date for revenues
            if ($countRevenues > 0) {
                $listDateRevenues = array_column($rsRevenues->toArray(), 'date');
            } else {
                $listDateRevenues = [];
            }

            // Get list date for costs
            if ($countCosts > 0) {
                $listDateCosts = array_column($rsCosts->toArray(), 'date');
            } else {
                $listDateCosts = [];
            }

            // Range date for label chartjs
            for ($currentDate = strtotime($startDate); $currentDate <= strtotime($endDate); $currentDate = strtotime('+1 day', $currentDate)) {
                $rangeDate    = date('Y-m-d', $currentDate);
                $labels[]     = $rangeDate;
                $revenue      = 0;
                $cus_debt     = 0;
                $cost         = 0;
                $cost_debt    = 0;

                // For Revenues (Doanh thu)
                list($revenue, $cus_debt) = $this->getDataForChart($rsRevenues, $rangeDate, $listDateRevenues);
                $revenues[]               = $revenue;
                $cus_debts[]              = $cus_debt;

                // For Costs (Chi phí)
                list($cost, $cost_debt)   = $this->getDataForChart($rsCosts, $rangeDate, $listDateCosts);
                $costs[]                  = $cost;
                $cost_debts[]             = $cost_debt;

                // For Profit (Lợi nhuận)
                $profit[] = $revenue - $cost;
            }
        } else {
            /**
             * Trường hợp "week, month, year" được nhóm nên không cần phải so sánh "day" như trên
             */

            // Create change model to array
            if ($countRevenues >= $countCosts) {
                $model = $rsRevenues->toArray();
            } else {
                $model = $rsCosts->toArray();
            }

            // Create "labels" for chartjs
            if ($filterDate === 'week') { // Nếu filterDate === week thì label hiển thị "Week 0: Y-m-d - Y-m-d"
                $labels = array_map(function($date) use ($filterDate) {
                    $week = Lang::get('admin.'. $filterDate) . ' ' . ($date['date'] + 1) . ' - Tháng ' . floor(($date['date'] / 4) + 1);
                    return $week;
                }, $model);
            } else {
                $labels = array_map(function($date) use ($filterDate) {
                    return Lang::get('admin.'. $filterDate) . ' ' . $date['date'];
                }, $model);
            }


            // Get Revenues
            foreach ($labels as $key => $item) {

                $revenue      = isset($rsRevenues[$key]->moneys) ? $rsRevenues[$key]->moneys : 0;
                $cus_prepay   = isset($rsRevenues[$key]->prepay) ? $rsRevenues[$key]->prepay : 0;
                $cost         = isset($rsCosts[$key]->moneys) ? $rsCosts[$key]->moneys : 0;
                $cost_debt    = isset($rsCosts[$key]->prepay) ? $rsCosts[$key]->prepay : 0;

                // For Revenues
                $revenues[] = $revenue; // Doanh thu

                if ($rsRevenues[$key]->status == '1') { // Check status da thanh toan chua?
                    $cus_debts[] = 0; // Không nợ
                } else {
                    $cus_debts[] = ($revenue - $cus_prepay); // Khách nợ
                }

                // For Costs
                $costs[] = $cost; // Chi phí
                if ($rsRevenues[$key]->status == '1') { // Check status da thanh toan chua?
                    $cost_debts[] = 0; // Nợ hàng
                } else {
                    $cost_debts[] = ($cost - $cost_debt); // Nợ hàng
                }

                // For Profit (Lợi nhuận)
                $profit[] = ($revenue - $cost);
            }
        }

        $datasets = [
            'labels'   => $labels,
            'datasets' => [
                [
                    'label'           => 'Doanh thu',
                    'data'            => $revenues,
                    'stack'           => 'Stack 0',
                    "fill"            => FALSE,
                    'borderColor'     => '#6699ff',
                    'backgroundColor' => 'rgb(54, 162, 235)',
                ],
                [
                    'label'           => 'Khách nợ',
                    'data'            => $cus_debts,
                    'stack'           => 'Stack 0',
                    "fill"            => FALSE,
                    'borderColor'     => '#ff1a1a',
                    'backgroundColor' => 'rgb(255, 99, 132)',
                ],
                [
                    'label'           => 'Chi phí',
                    'data'            => $costs,
                    'stack'           => 'Stack 1',
                    "fill"            => FALSE,
                    'borderColor'     => '#f67019',
                    'backgroundColor' => 'rgb(255, 159, 64)',
                ],
                [
                    'label'           => 'Nợ hàng',
                    'data'            => $cost_debts,
                    'stack'           => 'Stack 1',
                    "fill"            => FALSE,
                    'borderColor'     => '#58595b',
                    'backgroundColor' => 'rgb(201, 203, 207)',
                ],
                [
                    'label'           => 'Lợi nhuận',
                    'data'            => $profit,
                    'stack'           => 'Stack 2',
                    "fill"            => FALSE,
                    'borderColor'     => '#166a8f',
                    'backgroundColor' => 'rgb(75, 192, 192)',
                ],
            ]
        ];

        return $datasets;
    }

    /**
     * Get data for chartjs (using with filterDate = 'day')
     */
    private function getDataForChart($result, $rangeDate, &$listDate)
    {
        if (false !== $key = array_search($rangeDate, $listDate)) {
            // Set element data for chartjs
            $moneys       = isset($result[$key]->moneys) ? $result[$key]->moneys : 0;
            $prepay       = isset($result[$key]->prepay) ? $result[$key]->prepay : 0;

            // Check status da thanh toan chua?
            if ($result[$key]->status == '1') {
                $moneys_debts = 0; // Không nợ
            } else {
                $moneys_debts = $moneys - $prepay;
            }

            unset($listDate[$key]);
        } else {
            $moneys       = 0;
            $moneys_debts = 0;
        }
        return [
            $moneys,
            $moneys_debts
        ];
    }
}

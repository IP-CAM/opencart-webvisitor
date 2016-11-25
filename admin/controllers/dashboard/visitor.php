<?php

class ControllerDashboardVisitor extends Controller {

    public function index() {
        $this->load->language('dashboard/visitor');

        $data['heading_title'] = $this->language->get('heading_title');

        $data['text_day'] = $this->language->get('text_day');
        $data['text_week'] = $this->language->get('text_week');
        $data['text_month'] = $this->language->get('text_month');
        $data['text_year'] = $this->language->get('text_year');
        $data['text_view'] = $this->language->get('text_view');

        $data['token'] = $this->session->data['token'];
        
        $this->load->model('report/visitor');
        $this->model_report_visitor->checkAndcreateTable();
   
        $data['total_visitors'] = $this->model_report_visitor->totalVisitors();
        $data['total_hits'] = $this->model_report_visitor->totalHits();

        return $this->load->view('dashboard/visitor', $data);
    }

    public function chartvisitor() {
        $this->load->language('dashboard/visitor');
        $this->load->model('report/visitor');

        $data = array();

        $data['order'] = array();
        $data['customer'] = array();
        $data['xaxis'] = array();
        $data['labelValue'] = array();

        $data['order']['label'] = 'Total Visitor';
        $data['customer']['label'] = 'Total Hits';

        if (isset($this->request->get['range'])) {
            $range = $this->request->get['range'];
        } else {
            $range = 'month';
        }


        $totalVisitor = 0;
        $totalHits = 0;
        switch ($range) {
            case 'day':
                for ($i = 0; $i < 24; $i++) {
                    
                    $query = $this->model_report_visitor->getTodayVisitor(array('hour'=>$i));

                    if ($query->num_rows) {
                        $data['order']['data'][] = array($i, (int) $query->row['total']);
                        $totalVisitor += (int) $query->row['total'];
                    } else {
                        $data['order']['data'][] = array($i, 0);
                    }

                    $query2 = $this->model_report_visitor->getTodayHit(array('hour'=>$i));

                    if ($query2->num_rows) {
                        $data['customer']['data'][] = array($i, (int) $query2->row['total']);
                        $totalHits += (int) $query2->row['total'];
                    } else {
                        $data['customer']['data'][] = array($i, 0);
                    }
//
                    $data['xaxis'][] = array($i, date('H', mktime($i, 0, 0, date('n'), date('j'), date('Y'))));
                }
                break;
            case 'week':
                $date_start = strtotime('-' . date('w') . ' days');
                for ($i = 0; $i < 7; $i++) {
                    $date = date('Y-m-d', $date_start + ($i * 86400));
                    $query = $this->model_report_visitor->getWeekVisitor(array('date'=>$date));

                    if ($query->num_rows) {
                        $data['order']['data'][] = array($i, (int) $query->row['total']);
                        $totalVisitor += (int) $query->row['total'];
                    } else {
                        $data['order']['data'][] = array($i, 0);
                    }

                    $query2 = $this->model_report_visitor->getWeekHit(array('date'=>$date));

                    if ($query2->num_rows) {
                        $data['customer']['data'][] = array($i, (int) $query2->row['total']);
                        $totalHits += (int) $query2->row['total'];
                    } else {
                        $data['customer']['data'][] = array($i, 0);
                    }
                    $data['xaxis'][] = array($i, date('D', strtotime($date)));
                }

                break;
            default:
            case 'month':
                for ($i = 1; $i <= date('t'); $i++) {
                    $date = date('Y') . '-' . date('m') . '-' . $i;

                    $query = $this->model_report_visitor->getTotalVisitor(array('date' => $date));
                    if ($query->num_rows) {
                        $data['order']['data'][] = array($i, (int) $query->row['total']);
                        $totalVisitor += (int) $query->row['total'];
                    } else {
                        $data['order']['data'][] = array($i, 0);
                    }
                    
                    $query2 = $this->model_report_visitor->getTotalHits(array('date'=>$date));
                    if ($query2->num_rows) {
                        $data['customer']['data'][] = array($i, (int) $query2->row['total']);
                        $totalHits += (int) $query2->row['total'];
                    } else {
                        $data['customer']['data'][] = array($i, 0);
                    }

                    $data['xaxis'][] = array($i, date('j', strtotime($date)));
                }
                break;
            case 'year':
                for ($i = 1; $i <= 12; $i++) {
                    $query = $this->model_report_visitor->getYearVisitor(array('month'=>$i));

                    if ($query->num_rows) {
                        $data['order']['data'][] = array($i, (int) $query->row['total']);
                        $totalVisitor += (int) $query->row['total'];
                    } else {
                        $data['order']['data'][] = array($i, 0);
                    }

                    $query2 = $this->model_report_visitor->getYearHit(array('month'=>$i));

                    if ($query2->num_rows) {
                        $data['customer']['data'][] = array($i, (int) $query2->row['total']);
                        $totalHits += (int) $query2->row['total'];
                    } else {
                        $data['customer']['data'][] = array($i, 0);
                    }

                    $data['xaxis'][] = array($i, date('M', mktime(0, 0, 0, $i, 1, date('Y'))));
                }
                break;
        }

        $data['noVisitor'] = $totalVisitor;
        $data['noHit'] = $totalHits;
        $this->response->setOutput(json_encode($data));
    }

    public function chartvisit() {
        $this->load->language('dashboard/chart');

        $json = array();

        $this->load->model('report/sale');
        $this->load->model('report/customer');

        $json['order'] = array();
        $json['customer'] = array();
        $json['xaxis'] = array();

        $json['order']['label'] = $this->language->get('text_order');
        $json['customer']['label'] = $this->language->get('text_customer');
        $json['order']['data'] = array();
        $json['customer']['data'] = array();

        if (isset($this->request->get['range'])) {
            $range = $this->request->get['range'];
        } else {
            $range = 'day';
        }

        switch ($range) {
            default:
            case 'day':
                $results = $this->model_report_sale->getTotalOrdersByDay();

                foreach ($results as $key => $value) {
                    $json['order']['data'][] = array($key, $value['total']);
                }

                $results = $this->model_report_customer->getTotalCustomersByDay();

                foreach ($results as $key => $value) {
                    $json['customer']['data'][] = array($key, $value['total']);
                }

                for ($i = 0; $i < 24; $i++) {
                    $json['xaxis'][] = array($i, $i);
                }
                break;
            case 'week':
                $results = $this->model_report_sale->getTotalOrdersByWeek();

                foreach ($results as $key => $value) {
                    $json['order']['data'][] = array($key, $value['total']);
                }

                $results = $this->model_report_customer->getTotalCustomersByWeek();

                foreach ($results as $key => $value) {
                    $json['customer']['data'][] = array($key, $value['total']);
                }

                $date_start = strtotime('-' . date('w') . ' days');

                for ($i = 0; $i < 7; $i++) {
                    $date = date('Y-m-d', $date_start + ($i * 86400));

                    $json['xaxis'][] = array(date('w', strtotime($date)), date('D', strtotime($date)));
                }
                break;
            case 'month':
                $results = $this->model_report_sale->getTotalOrdersByMonth();

                foreach ($results as $key => $value) {
                    $json['order']['data'][] = array($key, $value['total']);
                }

                $results = $this->model_report_customer->getTotalCustomersByMonth();

                foreach ($results as $key => $value) {
                    $json['customer']['data'][] = array($key, $value['total']);
                }

                for ($i = 1; $i <= date('t'); $i++) {
                    $date = date('Y') . '-' . date('m') . '-' . $i;

                    $json['xaxis'][] = array(date('j', strtotime($date)), date('d', strtotime($date)));
                }
                break;
            case 'year':
                $results = $this->model_report_sale->getTotalOrdersByYear();

                foreach ($results as $key => $value) {
                    $json['order']['data'][] = array($key, $value['total']);
                }

                $results = $this->model_report_customer->getTotalCustomersByYear();

                foreach ($results as $key => $value) {
                    $json['customer']['data'][] = array($key, $value['total']);
                }

                for ($i = 1; $i <= 12; $i++) {
                    $json['xaxis'][] = array($i, date('M', mktime(0, 0, 0, $i)));
                }
                break;
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

}

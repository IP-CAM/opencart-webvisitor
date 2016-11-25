<?php

class ModelReportVisitor extends Model {

    public function checkAndcreateTable() {
        $query = $this->db->query("SHOW TABLES LIKE '" . DB_PREFIX . "visitor'");
        if(!$query->num_rows):
            $sql = "CREATE TABLE `".DB_PREFIX."visitor` (
            `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
            `ip` varchar(20) DEFAULT NULL,
            `datetime` datetime DEFAULT NULL,
            `hits` int(10) DEFAULT NULL,
            `online` varchar(255) DEFAULT NULL,
            `country` varchar(250) DEFAULT NULL,
            `geolocation` text,
            PRIMARY KEY (`id`)
          ) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;";
        $this->db->query($sql);
        endif;
        
        return true;
    }

    public function getTotalVisitor($data = array()) {
        $date = $data['date'];
        $sql = "SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "visitor` WHERE (DATE(datetime) = '" . $this->db->escape($date) . "') GROUP BY DAY(datetime)";
        $query = $this->db->query($sql);
//        return $query->row['total'];
        return $query;
    }

    public function getTodayVisitor($data = array()) {
        $sql = "SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "visitor` WHERE (DATE(datetime) = DATE(NOW()) AND HOUR(datetime) = '" . (int) $data['hour'] . "') GROUP BY HOUR(datetime) ORDER BY datetime ASC";
        $query = $this->db->query($sql);
        return $query;
    }

    public function getTodayHit($data = array()) {
        $sql = "SELECT sum(hits) AS total FROM " . DB_PREFIX . "visitor WHERE DATE(datetime) = DATE(NOW()) AND HOUR(datetime) = '" . (int) $data['hour'] . "' GROUP BY HOUR(datetime) ORDER BY datetime ASC";
        $query = $this->db->query($sql);
        return $query;
    }

    public function getWeekVisitor($data = array()) {
        $sql = "SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "visitor` WHERE DATE(datetime) = '" . $this->db->escape($data['date']) . "' GROUP BY DATE(datetime)";
        $query = $this->db->query($sql);
        return $query;
    }

    public function getWeekHit($data = array()) {
        $sql = "SELECT sum(hits) AS total FROM `" . DB_PREFIX . "visitor` WHERE DATE(datetime) = '" . $this->db->escape($data['date']) . "' GROUP BY DATE(datetime)";
        $query = $this->db->query($sql);
        return $query;
    }

    public function getYearVisitor($data = array()) {
        $sql = "SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "visitor` WHERE YEAR(datetime) = '" . date('Y') . "' AND MONTH(datetime) = '" . $data['month'] . "' GROUP BY MONTH(datetime)";
        $query = $this->db->query($sql);
        return $query;
    }

    public function getYearHit($data = array()) {
        $sql = "SELECT sum(hits) AS total FROM " . DB_PREFIX . "visitor WHERE YEAR(datetime) = '" . date('Y') . "' AND MONTH(datetime) = '" . $data['month'] . "' GROUP BY MONTH(datetime)";
        $query = $this->db->query($sql);
        return $query;
    }

    public function getTotalHits($data = array()) {
        $date = $data['date'];
        $sql = "SELECT sum(hits) AS total FROM " . DB_PREFIX . "visitor WHERE DATE(datetime) = '" . $this->db->escape($date) . "' GROUP BY DAY(datetime)";
        $query = $this->db->query($sql);
        return $query;
    }

    public function createVisitorTable() {
        $query = $this->db->query("CREATE TABLE IF NOT EXISTS " . DB_PREFIX . "visitor (`ip` varchar(20) NOT NULL default '', `datetime` date NOT NULL, `hits` int(10) NOT NULL default '1', `online` varchar(255) NOT NULL, PRIMARY KEY (ip, datetime))");
        return $query;
    }

    public function totalVisitors() {
        return $this->db->query("SELECT * FROM " . DB_PREFIX . "visitor GROUP BY ip")->num_rows;
    }

    public function totalHits() {
        return $this->db->query("SELECT SUM(hits) FROM " . DB_PREFIX . "visitor")->row["SUM(hits)"] + 0;
    }

    public function visitors($data = null) {
        $addSql = '';
        $dateFormat = '%d %M %Y';
        if ($data['filter_date_start'] == '' || $data['filter_date_end'] == ''):
            $addSql = 'WHERE MONTH(datetime) = MONTH(now()) GROUP BY date(datetime)';
        else :
            $addSql = "WHERE  date(datetime)>='" . $data['filter_date_start'] . "' AND date(datetime)<='" . $data['filter_date_end'] . "' GROUP BY month(datetime)";
            $dateFormat = '%M %Y';
        endif;
        $sql = "SELECT sum(hits) as hits, date_format(datetime,'" . $dateFormat . "') as date, count(ip) as ip FROM " . DB_PREFIX . "visitor " . $addSql;
        return $this->db->query($sql);
    }

    public function visitorsCountry($data) {
        $addSql = '';
        $dateFormat = '%d %M %Y';
        if ($data['filter_date_start'] == '' || $data['filter_date_end'] == ''):
            $addSql = ' MONTH(datetime) = MONTH(now()) GROUP BY country ORDER by hits desc';
        else :
            $addSql = "  date(datetime)>='" . $data['filter_date_start'] . "' AND date(datetime)<='" . $data['filter_date_end'] . "' GROUP BY country ORDER by hits desc";
            $dateFormat = '%M %Y';
        endif;
        $sql = "SELECT sum(hits) as hits, country FROM " . DB_PREFIX . "visitor WHERE $addSql ";
        return $this->db->query($sql);
    }

}

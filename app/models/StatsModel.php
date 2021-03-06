<?php
  require_once($GLOBALS['dirpre'].'models/Model.php');

  class StatsModel extends Model {
    private $dbstudent, $db;

    function __construct() {
      // Setup database
      $m = new MongoClient($GLOBALS['dburistudent']);
      $this->dbstudent = $m->$GLOBALS['dbnamestudent'];
      $m = new MongoClient($GLOBALS['dburi']);
      $this->db = $m->$GLOBALS['dbname'];
    }

    function countRecruiters() {
      return $this->db->recruiters->count();
    }
    function countJobListings() {
      return $this->db->jobs->count();
    }
    function getJobsMissingRecruiter() {
      $jobs = $this->db->jobs->find();

      global $MRecruiter;

      $norecruiter = array();
      foreach ($jobs as $job) {
        if (!$MRecruiter->IDexists($job['recruiter'])) {
          $norecruiter[] = $job;
        }
      }

      return $norecruiter;
    }
    function countCompanies() {
      return $this->db->companies->count();
    }
    function getIndustries() {
      $industries = $this->db->companies->find(array(), array('industry' => 1));
      $is = array();
      foreach ($industries as $i) {
        $is[] = $i['industry'];
      }
      return $is;
    }
    function getIndustriesByJobs() {
      global $MCompany;

      $industries = array();
      $jobs = $this->db->jobs->find();
      foreach ($jobs as $job) {
        $industry = $MCompany->getIndustry($job['company']);
        $industrysplit = explode(',', $industry);
        foreach ($industrysplit as $i) {
          $i = trim($i);
          if (strlen($i) > 0 and !in_array($i, $industries))
            $industries[] = $i;
        }
      }
      natsort($industries);

      return $industries;
    }
    function countSubletListings() {
      return $this->dbstudent->listings->count();
    }
    function countStudents() {
      return $this->dbstudent->emails->count();
    }
    function getStudentsConfirmed() {
      return $this->dbstudent->emails->find(array(
        'pass' => array('$exists' => true)
      ), array('email' => true));
    }
    function getStudentsUnconfirmed() {
      return $this->dbstudent->emails->find(array(
        'pass' => array('$exists' => false)
      ), array('email' => true));
    }
    function getCities($dojobs=false) {
      $cities = array();

      function addCity(&$cities, $doc) {
        if (isset($doc['city']) and isset($doc['state']))
          $city = getCity($doc['city'] . ', ' . $doc['state']);
        else
          $city = getCity($doc['location']);
        if ($city != null) {
          if (isset($cities[$city])) $cities[$city] ++;
          else $cities[$city] = 1;
        }
      }

      $sublets = $this->dbstudent->listings->find();
      foreach ($sublets as $doc) {
        addCity($cities, $doc);
      }
      if ($dojobs) {
        $jobs = $this->db->jobs->find();
        foreach ($jobs as $doc) {
          addCity($cities, $doc);
        }
      }

      return $cities;
    }
    function countCities() {
      return count($this->getCities());
    }
    function countUniversities() {
      global $S;
      return count($S->LUT);
    }
  }

  $MStats = new StatsModel();

?>
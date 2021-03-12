<?php
class Settings extends Controller
{

  /**
   * Class Constructor
   */
  public function __construct($registry)
  {
    // Load the controller constructor
    parent::__construct($registry);

    // Check if user is allowed admin access
    checkAdminAccess();

    // Load the settings model
    $this->settingModel = $this->load->model('cornerstone/setting', 'admin');

    // Set Breadcrumbs
    $this->data['breadcrumbs'] = array(
      array(
        'text' => 'Dashboard',
        'href' => get_site_url('admin')
      ),
      array(
        'text' => 'Settings',
        'href' => get_site_url('admin/settings')
      )
    );
  }

  /**
   * Index Page
   */
  public function index()
  {
    // Load view
    $this->load->view('settings/index', $this->data, 'admin');
    exit;
  }

  /**
   * Save Settings
   */
  public function save()
  {

    // Get type
    if (isset($_POST['set_type']) && !empty($_POST['set_type'])) {
      $settingType = htmlspecialchars(trim($_POST['set_type']));
    } else { // Unable to get type. Set error.
      flashMsg('admin_settings', '<strong>Error</strong> Sorry, there was any error saving those settings. Please contact your site administrator for debugging.', 'warning');
      redirectTo('admin/settings');
      exit;
    }

    // Check user is allowed to view this
    if (!empty($settingType) && !$this->role->canDo('edit_' . $settingType . '_settings')) {
      // Redirect user with error
      flashMsg('admin_settings', '<strong>Error</strong> Sorry, you are not allowed to edit the ' . $settingType . ' settings. Please contact your site administrator for access to this.', 'warning');
      redirectTo('admin/settings');
      exit;
    }

    //Check if page posted and process form if it is
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == "save") {

      // Sanitize POST data
      $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

      // Try validating
      try {

        // Get setting data
        $this->data['setting'] = (isset($_POST['setting'])) ? $_POST['setting'] : array();
        if (empty($this->data['setting'])) {
          // Data not set. Return error.
          $this->data['err']['setting'] = 'There was an error updating the settings';
          flashMsg('settings_' . $settingType, '<strong>Error</strong> here was an error updating the ' . ucfirst($settingType) . ' settings. Please try again.', 'warning');
        }
      } catch (Exception $e) {

        // Log error if any and set flash message
        error_log($e->getMessage(), 0);
        flashMsg('settings_' . $settingType, '<strong>Error</strong> There was an error updating the ' . ucfirst($settingType) . ' settings. Please try again.', 'warning');
      }

      // If valid, update
      if (empty($this->data['err'])) {
        // Validated

        // Init total updated
        $totalUpdated = 0;

        // Loop through submitted information
        foreach ($this->data['setting'] as $setting => $values) {
          // Get setting
          $settingName = htmlspecialchars(trim($setting));
          // Get current value
          $this->data['cur_' . $settingName] = htmlspecialchars(trim($values['current']));
          // Get set value
          if (isset($values['set']) && !is_array($values['set'])) {
            $this->data['set_' . $settingName] = htmlspecialchars(trim($values['set']));
          } else if (isset($values['set']) && is_array($values['set'])) {
            // Sort the array
            ksort($values['set']);
            // Set the value
            $this->data['set_' . $settingName] = htmlspecialchars(trim(implode(',', $values['set'])));
          } else {
            $this->data['set_' . $settingName] = "0";
          }

          // Strip content if site_url
          $this->data['set_' . $settingName] = ($settingName === "site_url") ? str_replace(array('http://', 'https://', '//'), '', rtrim($this->data['set_' . $settingName], '/')) : $this->data['set_' . $settingName];

          // Check if boolean value
          $this->data['cur_' . $settingName] = (isset($values['bool']) && empty($this->data['cur_' . $settingName])) ? "0" : $this->data['cur_' . $settingName];
          $this->data['set_' . $settingName] = (isset($values['bool']) && $this->data['set_' . $settingName] == "on") ? "1" : $this->data['set_' . $settingName];
          if (isset($values['bool'])) {
            $this->data[$settingName . '_bool'] = TRUE;
          }

          // Check if the value is different
          if ($this->data['cur_' . $settingName] !== $this->data['set_' . $settingName]) {
            // Update setting
            if ($this->settingModel->editOption(
              $settingName,
              $this->data['set_' . $settingName]
            )) {
              // Updated
              $totalUpdated++;
            }
          }
        }

        // Set success message
        flashMsg('admin_settings', '<strong>Success</strong>' . ucfirst($settingType) . ' settings saved with ' . $totalUpdated . ' option(s) updated successfully.');

        // Return to page
        redirectTo('admin/settings');
        exit;
      }

      // If it's made it this far there were errors. Redirect to page with data

      // Load method
      $this->$settingType(TRUE);
      exit;
    } // Failed to validate. Return to main settings page

    // Set error
    flashMsg('admin_settings', '<strong>Error</strong> There was an saving the settings. Please try again', 'warning');

    // Redirect
    redirectTo('admin/settings');
    exit;
  }

  /**
   * Timezone list
   */
  protected function getTimezoneList()
  {
    /**
     * List of timezones from https://gist.github.com/kulbakin/7498458. Retrieved 15/04/2020
     */
    return array(
      'Pacific/Midway' => '(UTC-11:00) Midway',
      'Pacific/Niue' => '(UTC-11:00) Niue',
      'Pacific/Pago_Pago' => '(UTC-11:00) Pago Pago',
      'America/Adak' => '(UTC-10:00) Adak',
      'Pacific/Honolulu' => '(UTC-10:00) Honolulu',
      'Pacific/Johnston' => '(UTC-10:00) Johnston',
      'Pacific/Rarotonga' => '(UTC-10:00) Rarotonga',
      'Pacific/Tahiti' => '(UTC-10:00) Tahiti',
      'Pacific/Marquesas' => '(UTC-09:30) Marquesas',
      'America/Anchorage' => '(UTC-09:00) Anchorage',
      'Pacific/Gambier' => '(UTC-09:00) Gambier',
      'America/Juneau' => '(UTC-09:00) Juneau',
      'America/Nome' => '(UTC-09:00) Nome',
      'America/Sitka' => '(UTC-09:00) Sitka',
      'America/Yakutat' => '(UTC-09:00) Yakutat',
      'America/Dawson' => '(UTC-08:00) Dawson',
      'America/Los_Angeles' => '(UTC-08:00) Los Angeles',
      'America/Metlakatla' => '(UTC-08:00) Metlakatla',
      'Pacific/Pitcairn' => '(UTC-08:00) Pitcairn',
      'America/Santa_Isabel' => '(UTC-08:00) Santa Isabel',
      'America/Tijuana' => '(UTC-08:00) Tijuana',
      'America/Vancouver' => '(UTC-08:00) Vancouver',
      'America/Whitehorse' => '(UTC-08:00) Whitehorse',
      'America/Boise' => '(UTC-07:00) Boise',
      'America/Cambridge_Bay' => '(UTC-07:00) Cambridge Bay',
      'America/Chihuahua' => '(UTC-07:00) Chihuahua',
      'America/Creston' => '(UTC-07:00) Creston',
      'America/Dawson_Creek' => '(UTC-07:00) Dawson Creek',
      'America/Denver' => '(UTC-07:00) Denver',
      'America/Edmonton' => '(UTC-07:00) Edmonton',
      'America/Hermosillo' => '(UTC-07:00) Hermosillo',
      'America/Inuvik' => '(UTC-07:00) Inuvik',
      'America/Mazatlan' => '(UTC-07:00) Mazatlan',
      'America/Ojinaga' => '(UTC-07:00) Ojinaga',
      'America/Phoenix' => '(UTC-07:00) Phoenix',
      'America/Shiprock' => '(UTC-07:00) Shiprock',
      'America/Yellowknife' => '(UTC-07:00) Yellowknife',
      'America/Bahia_Banderas' => '(UTC-06:00) Bahia Banderas',
      'America/Belize' => '(UTC-06:00) Belize',
      'America/North_Dakota/Beulah' => '(UTC-06:00) Beulah',
      'America/Cancun' => '(UTC-06:00) Cancun',
      'America/North_Dakota/Center' => '(UTC-06:00) Center',
      'America/Chicago' => '(UTC-06:00) Chicago',
      'America/Costa_Rica' => '(UTC-06:00) Costa Rica',
      'Pacific/Easter' => '(UTC-06:00) Easter',
      'America/El_Salvador' => '(UTC-06:00) El Salvador',
      'Pacific/Galapagos' => '(UTC-06:00) Galapagos',
      'America/Guatemala' => '(UTC-06:00) Guatemala',
      'America/Indiana/Knox' => '(UTC-06:00) Knox',
      'America/Managua' => '(UTC-06:00) Managua',
      'America/Matamoros' => '(UTC-06:00) Matamoros',
      'America/Menominee' => '(UTC-06:00) Menominee',
      'America/Merida' => '(UTC-06:00) Merida',
      'America/Mexico_City' => '(UTC-06:00) Mexico City',
      'America/Monterrey' => '(UTC-06:00) Monterrey',
      'America/North_Dakota/New_Salem' => '(UTC-06:00) New Salem',
      'America/Rainy_River' => '(UTC-06:00) Rainy River',
      'America/Rankin_Inlet' => '(UTC-06:00) Rankin Inlet',
      'America/Regina' => '(UTC-06:00) Regina',
      'America/Resolute' => '(UTC-06:00) Resolute',
      'America/Swift_Current' => '(UTC-06:00) Swift Current',
      'America/Tegucigalpa' => '(UTC-06:00) Tegucigalpa',
      'America/Indiana/Tell_City' => '(UTC-06:00) Tell City',
      'America/Winnipeg' => '(UTC-06:00) Winnipeg',
      'America/Atikokan' => '(UTC-05:00) Atikokan',
      'America/Bogota' => '(UTC-05:00) Bogota',
      'America/Cayman' => '(UTC-05:00) Cayman',
      'America/Detroit' => '(UTC-05:00) Detroit',
      'America/Grand_Turk' => '(UTC-05:00) Grand Turk',
      'America/Guayaquil' => '(UTC-05:00) Guayaquil',
      'America/Havana' => '(UTC-05:00) Havana',
      'America/Indiana/Indianapolis' => '(UTC-05:00) Indianapolis',
      'America/Iqaluit' => '(UTC-05:00) Iqaluit',
      'America/Jamaica' => '(UTC-05:00) Jamaica',
      'America/Lima' => '(UTC-05:00) Lima',
      'America/Kentucky/Louisville' => '(UTC-05:00) Louisville',
      'America/Indiana/Marengo' => '(UTC-05:00) Marengo',
      'America/Kentucky/Monticello' => '(UTC-05:00) Monticello',
      'America/Montreal' => '(UTC-05:00) Montreal',
      'America/Nassau' => '(UTC-05:00) Nassau',
      'America/New_York' => '(UTC-05:00) New York',
      'America/Nipigon' => '(UTC-05:00) Nipigon',
      'America/Panama' => '(UTC-05:00) Panama',
      'America/Pangnirtung' => '(UTC-05:00) Pangnirtung',
      'America/Indiana/Petersburg' => '(UTC-05:00) Petersburg',
      'America/Port-au-Prince' => '(UTC-05:00) Port-au-Prince',
      'America/Thunder_Bay' => '(UTC-05:00) Thunder Bay',
      'America/Toronto' => '(UTC-05:00) Toronto',
      'America/Indiana/Vevay' => '(UTC-05:00) Vevay',
      'America/Indiana/Vincennes' => '(UTC-05:00) Vincennes',
      'America/Indiana/Winamac' => '(UTC-05:00) Winamac',
      'America/Caracas' => '(UTC-04:30) Caracas',
      'America/Anguilla' => '(UTC-04:00) Anguilla',
      'America/Antigua' => '(UTC-04:00) Antigua',
      'America/Aruba' => '(UTC-04:00) Aruba',
      'America/Asuncion' => '(UTC-04:00) Asuncion',
      'America/Barbados' => '(UTC-04:00) Barbados',
      'Atlantic/Bermuda' => '(UTC-04:00) Bermuda',
      'America/Blanc-Sablon' => '(UTC-04:00) Blanc-Sablon',
      'America/Boa_Vista' => '(UTC-04:00) Boa Vista',
      'America/Campo_Grande' => '(UTC-04:00) Campo Grande',
      'America/Cuiaba' => '(UTC-04:00) Cuiaba',
      'America/Curacao' => '(UTC-04:00) Curacao',
      'America/Dominica' => '(UTC-04:00) Dominica',
      'America/Eirunepe' => '(UTC-04:00) Eirunepe',
      'America/Glace_Bay' => '(UTC-04:00) Glace Bay',
      'America/Goose_Bay' => '(UTC-04:00) Goose Bay',
      'America/Grenada' => '(UTC-04:00) Grenada',
      'America/Guadeloupe' => '(UTC-04:00) Guadeloupe',
      'America/Guyana' => '(UTC-04:00) Guyana',
      'America/Halifax' => '(UTC-04:00) Halifax',
      'America/Kralendijk' => '(UTC-04:00) Kralendijk',
      'America/La_Paz' => '(UTC-04:00) La Paz',
      'America/Lower_Princes' => '(UTC-04:00) Lower Princes',
      'America/Manaus' => '(UTC-04:00) Manaus',
      'America/Marigot' => '(UTC-04:00) Marigot',
      'America/Martinique' => '(UTC-04:00) Martinique',
      'America/Moncton' => '(UTC-04:00) Moncton',
      'America/Montserrat' => '(UTC-04:00) Montserrat',
      'Antarctica/Palmer' => '(UTC-04:00) Palmer',
      'America/Port_of_Spain' => '(UTC-04:00) Port of Spain',
      'America/Porto_Velho' => '(UTC-04:00) Porto Velho',
      'America/Puerto_Rico' => '(UTC-04:00) Puerto Rico',
      'America/Rio_Branco' => '(UTC-04:00) Rio Branco',
      'America/Santiago' => '(UTC-04:00) Santiago',
      'America/Santo_Domingo' => '(UTC-04:00) Santo Domingo',
      'America/St_Barthelemy' => '(UTC-04:00) St. Barthelemy',
      'America/St_Kitts' => '(UTC-04:00) St. Kitts',
      'America/St_Lucia' => '(UTC-04:00) St. Lucia',
      'America/St_Thomas' => '(UTC-04:00) St. Thomas',
      'America/St_Vincent' => '(UTC-04:00) St. Vincent',
      'America/Thule' => '(UTC-04:00) Thule',
      'America/Tortola' => '(UTC-04:00) Tortola',
      'America/St_Johns' => '(UTC-03:30) St. Johns',
      'America/Araguaina' => '(UTC-03:00) Araguaina',
      'America/Bahia' => '(UTC-03:00) Bahia',
      'America/Belem' => '(UTC-03:00) Belem',
      'America/Argentina/Buenos_Aires' => '(UTC-03:00) Buenos Aires',
      'America/Argentina/Catamarca' => '(UTC-03:00) Catamarca',
      'America/Cayenne' => '(UTC-03:00) Cayenne',
      'America/Argentina/Cordoba' => '(UTC-03:00) Cordoba',
      'America/Fortaleza' => '(UTC-03:00) Fortaleza',
      'America/Godthab' => '(UTC-03:00) Godthab',
      'America/Argentina/Jujuy' => '(UTC-03:00) Jujuy',
      'America/Argentina/La_Rioja' => '(UTC-03:00) La Rioja',
      'America/Maceio' => '(UTC-03:00) Maceio',
      'America/Argentina/Mendoza' => '(UTC-03:00) Mendoza',
      'America/Miquelon' => '(UTC-03:00) Miquelon',
      'America/Montevideo' => '(UTC-03:00) Montevideo',
      'America/Paramaribo' => '(UTC-03:00) Paramaribo',
      'America/Recife' => '(UTC-03:00) Recife',
      'America/Argentina/Rio_Gallegos' => '(UTC-03:00) Rio Gallegos',
      'Antarctica/Rothera' => '(UTC-03:00) Rothera',
      'America/Argentina/Salta' => '(UTC-03:00) Salta',
      'America/Argentina/San_Juan' => '(UTC-03:00) San Juan',
      'America/Argentina/San_Luis' => '(UTC-03:00) San Luis',
      'America/Santarem' => '(UTC-03:00) Santarem',
      'America/Sao_Paulo' => '(UTC-03:00) Sao Paulo',
      'Atlantic/Stanley' => '(UTC-03:00) Stanley',
      'America/Argentina/Tucuman' => '(UTC-03:00) Tucuman',
      'America/Argentina/Ushuaia' => '(UTC-03:00) Ushuaia',
      'America/Noronha' => '(UTC-02:00) Noronha',
      'Atlantic/South_Georgia' => '(UTC-02:00) South Georgia',
      'Atlantic/Azores' => '(UTC-01:00) Azores',
      'Atlantic/Cape_Verde' => '(UTC-01:00) Cape Verde',
      'America/Scoresbysund' => '(UTC-01:00) Scoresbysund',
      'Africa/Abidjan' => '(UTC+00:00) Abidjan',
      'Africa/Accra' => '(UTC+00:00) Accra',
      'Africa/Bamako' => '(UTC+00:00) Bamako',
      'Africa/Banjul' => '(UTC+00:00) Banjul',
      'Africa/Bissau' => '(UTC+00:00) Bissau',
      'Atlantic/Canary' => '(UTC+00:00) Canary',
      'Africa/Casablanca' => '(UTC+00:00) Casablanca',
      'Africa/Conakry' => '(UTC+00:00) Conakry',
      'Africa/Dakar' => '(UTC+00:00) Dakar',
      'America/Danmarkshavn' => '(UTC+00:00) Danmarkshavn',
      'Europe/Dublin' => '(UTC+00:00) Dublin',
      'Africa/El_Aaiun' => '(UTC+00:00) El Aaiun',
      'Atlantic/Faroe' => '(UTC+00:00) Faroe',
      'Africa/Freetown' => '(UTC+00:00) Freetown',
      'Europe/Guernsey' => '(UTC+00:00) Guernsey',
      'Europe/Isle_of_Man' => '(UTC+00:00) Isle of Man',
      'Europe/Jersey' => '(UTC+00:00) Jersey',
      'Europe/Lisbon' => '(UTC+00:00) Lisbon',
      'Africa/Lome' => '(UTC+00:00) Lome',
      'Europe/London' => '(UTC+00:00) London',
      'Atlantic/Madeira' => '(UTC+00:00) Madeira',
      'Africa/Monrovia' => '(UTC+00:00) Monrovia',
      'Africa/Nouakchott' => '(UTC+00:00) Nouakchott',
      'Africa/Ouagadougou' => '(UTC+00:00) Ouagadougou',
      'Atlantic/Reykjavik' => '(UTC+00:00) Reykjavik',
      'Africa/Sao_Tome' => '(UTC+00:00) Sao Tome',
      'Atlantic/St_Helena' => '(UTC+00:00) St. Helena',
      'UTC' => '(UTC+00:00) UTC',
      'Africa/Algiers' => '(UTC+01:00) Algiers',
      'Europe/Amsterdam' => '(UTC+01:00) Amsterdam',
      'Europe/Andorra' => '(UTC+01:00) Andorra',
      'Africa/Bangui' => '(UTC+01:00) Bangui',
      'Europe/Belgrade' => '(UTC+01:00) Belgrade',
      'Europe/Berlin' => '(UTC+01:00) Berlin',
      'Europe/Bratislava' => '(UTC+01:00) Bratislava',
      'Africa/Brazzaville' => '(UTC+01:00) Brazzaville',
      'Europe/Brussels' => '(UTC+01:00) Brussels',
      'Europe/Budapest' => '(UTC+01:00) Budapest',
      'Europe/Busingen' => '(UTC+01:00) Busingen',
      'Africa/Ceuta' => '(UTC+01:00) Ceuta',
      'Europe/Copenhagen' => '(UTC+01:00) Copenhagen',
      'Africa/Douala' => '(UTC+01:00) Douala',
      'Europe/Gibraltar' => '(UTC+01:00) Gibraltar',
      'Africa/Kinshasa' => '(UTC+01:00) Kinshasa',
      'Africa/Lagos' => '(UTC+01:00) Lagos',
      'Africa/Libreville' => '(UTC+01:00) Libreville',
      'Europe/Ljubljana' => '(UTC+01:00) Ljubljana',
      'Arctic/Longyearbyen' => '(UTC+01:00) Longyearbyen',
      'Africa/Luanda' => '(UTC+01:00) Luanda',
      'Europe/Luxembourg' => '(UTC+01:00) Luxembourg',
      'Europe/Madrid' => '(UTC+01:00) Madrid',
      'Africa/Malabo' => '(UTC+01:00) Malabo',
      'Europe/Malta' => '(UTC+01:00) Malta',
      'Europe/Monaco' => '(UTC+01:00) Monaco',
      'Africa/Ndjamena' => '(UTC+01:00) Ndjamena',
      'Africa/Niamey' => '(UTC+01:00) Niamey',
      'Europe/Oslo' => '(UTC+01:00) Oslo',
      'Europe/Paris' => '(UTC+01:00) Paris',
      'Europe/Podgorica' => '(UTC+01:00) Podgorica',
      'Africa/Porto-Novo' => '(UTC+01:00) Porto-Novo',
      'Europe/Prague' => '(UTC+01:00) Prague',
      'Europe/Rome' => '(UTC+01:00) Rome',
      'Europe/San_Marino' => '(UTC+01:00) San Marino',
      'Europe/Sarajevo' => '(UTC+01:00) Sarajevo',
      'Europe/Skopje' => '(UTC+01:00) Skopje',
      'Europe/Stockholm' => '(UTC+01:00) Stockholm',
      'Europe/Tirane' => '(UTC+01:00) Tirane',
      'Africa/Tripoli' => '(UTC+01:00) Tripoli',
      'Africa/Tunis' => '(UTC+01:00) Tunis',
      'Europe/Vaduz' => '(UTC+01:00) Vaduz',
      'Europe/Vatican' => '(UTC+01:00) Vatican',
      'Europe/Vienna' => '(UTC+01:00) Vienna',
      'Europe/Warsaw' => '(UTC+01:00) Warsaw',
      'Africa/Windhoek' => '(UTC+01:00) Windhoek',
      'Europe/Zagreb' => '(UTC+01:00) Zagreb',
      'Europe/Zurich' => '(UTC+01:00) Zurich',
      'Europe/Athens' => '(UTC+02:00) Athens',
      'Asia/Beirut' => '(UTC+02:00) Beirut',
      'Africa/Blantyre' => '(UTC+02:00) Blantyre',
      'Europe/Bucharest' => '(UTC+02:00) Bucharest',
      'Africa/Bujumbura' => '(UTC+02:00) Bujumbura',
      'Africa/Cairo' => '(UTC+02:00) Cairo',
      'Europe/Chisinau' => '(UTC+02:00) Chisinau',
      'Asia/Damascus' => '(UTC+02:00) Damascus',
      'Africa/Gaborone' => '(UTC+02:00) Gaborone',
      'Asia/Gaza' => '(UTC+02:00) Gaza',
      'Africa/Harare' => '(UTC+02:00) Harare',
      'Asia/Hebron' => '(UTC+02:00) Hebron',
      'Europe/Helsinki' => '(UTC+02:00) Helsinki',
      'Europe/Istanbul' => '(UTC+02:00) Istanbul',
      'Asia/Jerusalem' => '(UTC+02:00) Jerusalem',
      'Africa/Johannesburg' => '(UTC+02:00) Johannesburg',
      'Europe/Kiev' => '(UTC+02:00) Kiev',
      'Africa/Kigali' => '(UTC+02:00) Kigali',
      'Africa/Lubumbashi' => '(UTC+02:00) Lubumbashi',
      'Africa/Lusaka' => '(UTC+02:00) Lusaka',
      'Africa/Maputo' => '(UTC+02:00) Maputo',
      'Europe/Mariehamn' => '(UTC+02:00) Mariehamn',
      'Africa/Maseru' => '(UTC+02:00) Maseru',
      'Africa/Mbabane' => '(UTC+02:00) Mbabane',
      'Asia/Nicosia' => '(UTC+02:00) Nicosia',
      'Europe/Riga' => '(UTC+02:00) Riga',
      'Europe/Simferopol' => '(UTC+02:00) Simferopol',
      'Europe/Sofia' => '(UTC+02:00) Sofia',
      'Europe/Tallinn' => '(UTC+02:00) Tallinn',
      'Europe/Uzhgorod' => '(UTC+02:00) Uzhgorod',
      'Europe/Vilnius' => '(UTC+02:00) Vilnius',
      'Europe/Zaporozhye' => '(UTC+02:00) Zaporozhye',
      'Africa/Addis_Ababa' => '(UTC+03:00) Addis Ababa',
      'Asia/Aden' => '(UTC+03:00) Aden',
      'Asia/Amman' => '(UTC+03:00) Amman',
      'Indian/Antananarivo' => '(UTC+03:00) Antananarivo',
      'Africa/Asmara' => '(UTC+03:00) Asmara',
      'Asia/Baghdad' => '(UTC+03:00) Baghdad',
      'Asia/Bahrain' => '(UTC+03:00) Bahrain',
      'Indian/Comoro' => '(UTC+03:00) Comoro',
      'Africa/Dar_es_Salaam' => '(UTC+03:00) Dar es Salaam',
      'Africa/Djibouti' => '(UTC+03:00) Djibouti',
      'Africa/Juba' => '(UTC+03:00) Juba',
      'Europe/Kaliningrad' => '(UTC+03:00) Kaliningrad',
      'Africa/Kampala' => '(UTC+03:00) Kampala',
      'Africa/Khartoum' => '(UTC+03:00) Khartoum',
      'Asia/Kuwait' => '(UTC+03:00) Kuwait',
      'Indian/Mayotte' => '(UTC+03:00) Mayotte',
      'Europe/Minsk' => '(UTC+03:00) Minsk',
      'Africa/Mogadishu' => '(UTC+03:00) Mogadishu',
      'Africa/Nairobi' => '(UTC+03:00) Nairobi',
      'Asia/Qatar' => '(UTC+03:00) Qatar',
      'Asia/Riyadh' => '(UTC+03:00) Riyadh',
      'Antarctica/Syowa' => '(UTC+03:00) Syowa',
      'Asia/Tehran' => '(UTC+03:30) Tehran',
      'Asia/Baku' => '(UTC+04:00) Baku',
      'Asia/Dubai' => '(UTC+04:00) Dubai',
      'Indian/Mahe' => '(UTC+04:00) Mahe',
      'Indian/Mauritius' => '(UTC+04:00) Mauritius',
      'Europe/Moscow' => '(UTC+04:00) Moscow',
      'Asia/Muscat' => '(UTC+04:00) Muscat',
      'Indian/Reunion' => '(UTC+04:00) Reunion',
      'Europe/Samara' => '(UTC+04:00) Samara',
      'Asia/Tbilisi' => '(UTC+04:00) Tbilisi',
      'Europe/Volgograd' => '(UTC+04:00) Volgograd',
      'Asia/Yerevan' => '(UTC+04:00) Yerevan',
      'Asia/Kabul' => '(UTC+04:30) Kabul',
      'Asia/Aqtau' => '(UTC+05:00) Aqtau',
      'Asia/Aqtobe' => '(UTC+05:00) Aqtobe',
      'Asia/Ashgabat' => '(UTC+05:00) Ashgabat',
      'Asia/Dushanbe' => '(UTC+05:00) Dushanbe',
      'Asia/Karachi' => '(UTC+05:00) Karachi',
      'Indian/Kerguelen' => '(UTC+05:00) Kerguelen',
      'Indian/Maldives' => '(UTC+05:00) Maldives',
      'Antarctica/Mawson' => '(UTC+05:00) Mawson',
      'Asia/Oral' => '(UTC+05:00) Oral',
      'Asia/Samarkand' => '(UTC+05:00) Samarkand',
      'Asia/Tashkent' => '(UTC+05:00) Tashkent',
      'Asia/Colombo' => '(UTC+05:30) Colombo',
      'Asia/Kolkata' => '(UTC+05:30) Kolkata',
      'Asia/Kathmandu' => '(UTC+05:45) Kathmandu',
      'Asia/Almaty' => '(UTC+06:00) Almaty',
      'Asia/Bishkek' => '(UTC+06:00) Bishkek',
      'Indian/Chagos' => '(UTC+06:00) Chagos',
      'Asia/Dhaka' => '(UTC+06:00) Dhaka',
      'Asia/Qyzylorda' => '(UTC+06:00) Qyzylorda',
      'Asia/Thimphu' => '(UTC+06:00) Thimphu',
      'Antarctica/Vostok' => '(UTC+06:00) Vostok',
      'Asia/Yekaterinburg' => '(UTC+06:00) Yekaterinburg',
      'Indian/Cocos' => '(UTC+06:30) Cocos',
      'Asia/Rangoon' => '(UTC+06:30) Rangoon',
      'Asia/Bangkok' => '(UTC+07:00) Bangkok',
      'Indian/Christmas' => '(UTC+07:00) Christmas',
      'Antarctica/Davis' => '(UTC+07:00) Davis',
      'Asia/Ho_Chi_Minh' => '(UTC+07:00) Ho Chi Minh',
      'Asia/Hovd' => '(UTC+07:00) Hovd',
      'Asia/Jakarta' => '(UTC+07:00) Jakarta',
      'Asia/Novokuznetsk' => '(UTC+07:00) Novokuznetsk',
      'Asia/Novosibirsk' => '(UTC+07:00) Novosibirsk',
      'Asia/Omsk' => '(UTC+07:00) Omsk',
      'Asia/Phnom_Penh' => '(UTC+07:00) Phnom Penh',
      'Asia/Pontianak' => '(UTC+07:00) Pontianak',
      'Asia/Vientiane' => '(UTC+07:00) Vientiane',
      'Asia/Brunei' => '(UTC+08:00) Brunei',
      'Antarctica/Casey' => '(UTC+08:00) Casey',
      'Asia/Choibalsan' => '(UTC+08:00) Choibalsan',
      'Asia/Chongqing' => '(UTC+08:00) Chongqing',
      'Asia/Harbin' => '(UTC+08:00) Harbin',
      'Asia/Hong_Kong' => '(UTC+08:00) Hong Kong',
      'Asia/Kashgar' => '(UTC+08:00) Kashgar',
      'Asia/Krasnoyarsk' => '(UTC+08:00) Krasnoyarsk',
      'Asia/Kuala_Lumpur' => '(UTC+08:00) Kuala Lumpur',
      'Asia/Kuching' => '(UTC+08:00) Kuching',
      'Asia/Macau' => '(UTC+08:00) Macau',
      'Asia/Makassar' => '(UTC+08:00) Makassar',
      'Asia/Manila' => '(UTC+08:00) Manila',
      'Australia/Perth' => '(UTC+08:00) Perth',
      'Asia/Shanghai' => '(UTC+08:00) Shanghai',
      'Asia/Singapore' => '(UTC+08:00) Singapore',
      'Asia/Taipei' => '(UTC+08:00) Taipei',
      'Asia/Ulaanbaatar' => '(UTC+08:00) Ulaanbaatar',
      'Asia/Urumqi' => '(UTC+08:00) Urumqi',
      'Australia/Eucla' => '(UTC+08:45) Eucla',
      'Asia/Dili' => '(UTC+09:00) Dili',
      'Asia/Irkutsk' => '(UTC+09:00) Irkutsk',
      'Asia/Jayapura' => '(UTC+09:00) Jayapura',
      'Pacific/Palau' => '(UTC+09:00) Palau',
      'Asia/Pyongyang' => '(UTC+09:00) Pyongyang',
      'Asia/Seoul' => '(UTC+09:00) Seoul',
      'Asia/Tokyo' => '(UTC+09:00) Tokyo',
      'Australia/Adelaide' => '(UTC+09:30) Adelaide',
      'Australia/Broken_Hill' => '(UTC+09:30) Broken Hill',
      'Australia/Darwin' => '(UTC+09:30) Darwin',
      'Australia/Brisbane' => '(UTC+10:00) Brisbane',
      'Pacific/Chuuk' => '(UTC+10:00) Chuuk',
      'Australia/Currie' => '(UTC+10:00) Currie',
      'Antarctica/DumontDUrville' => '(UTC+10:00) DumontDUrville',
      'Pacific/Guam' => '(UTC+10:00) Guam',
      'Australia/Hobart' => '(UTC+10:00) Hobart',
      'Asia/Khandyga' => '(UTC+10:00) Khandyga',
      'Australia/Lindeman' => '(UTC+10:00) Lindeman',
      'Australia/Melbourne' => '(UTC+10:00) Melbourne',
      'Pacific/Port_Moresby' => '(UTC+10:00) Port Moresby',
      'Pacific/Saipan' => '(UTC+10:00) Saipan',
      'Australia/Sydney' => '(UTC+10:00) Sydney',
      'Asia/Yakutsk' => '(UTC+10:00) Yakutsk',
      'Australia/Lord_Howe' => '(UTC+10:30) Lord Howe',
      'Pacific/Efate' => '(UTC+11:00) Efate',
      'Pacific/Guadalcanal' => '(UTC+11:00) Guadalcanal',
      'Pacific/Kosrae' => '(UTC+11:00) Kosrae',
      'Antarctica/Macquarie' => '(UTC+11:00) Macquarie',
      'Pacific/Noumea' => '(UTC+11:00) Noumea',
      'Pacific/Pohnpei' => '(UTC+11:00) Pohnpei',
      'Asia/Sakhalin' => '(UTC+11:00) Sakhalin',
      'Asia/Ust-Nera' => '(UTC+11:00) Ust-Nera',
      'Asia/Vladivostok' => '(UTC+11:00) Vladivostok',
      'Pacific/Norfolk' => '(UTC+11:30) Norfolk',
      'Asia/Anadyr' => '(UTC+12:00) Anadyr',
      'Pacific/Auckland' => '(UTC+12:00) Auckland',
      'Pacific/Fiji' => '(UTC+12:00) Fiji',
      'Pacific/Funafuti' => '(UTC+12:00) Funafuti',
      'Asia/Kamchatka' => '(UTC+12:00) Kamchatka',
      'Pacific/Kwajalein' => '(UTC+12:00) Kwajalein',
      'Asia/Magadan' => '(UTC+12:00) Magadan',
      'Pacific/Majuro' => '(UTC+12:00) Majuro',
      'Antarctica/McMurdo' => '(UTC+12:00) McMurdo',
      'Pacific/Nauru' => '(UTC+12:00) Nauru',
      'Antarctica/South_Pole' => '(UTC+12:00) South Pole',
      'Pacific/Tarawa' => '(UTC+12:00) Tarawa',
      'Pacific/Wake' => '(UTC+12:00) Wake',
      'Pacific/Wallis' => '(UTC+12:00) Wallis',
      'Pacific/Chatham' => '(UTC+12:45) Chatham',
      'Pacific/Apia' => '(UTC+13:00) Apia',
      'Pacific/Enderbury' => '(UTC+13:00) Enderbury',
      'Pacific/Fakaofo' => '(UTC+13:00) Fakaofo',
      'Pacific/Tongatapu' => '(UTC+13:00) Tongatapu',
      'Pacific/Kiritimati' => '(UTC+14:00) Kiritimati',
    );
  }

  /**
   * Get timezone options
   *
   * @param string $currentTimezone `[optional]` The currently selected timezone. Defaults to the current timezone.
   */
  protected function getTimezoneOptions(string $currentTimezone = null)
  {

    // Set fallback current timezone
    $currentTimezone = (empty($currentTimezone)) ? date_default_timezone_get() : $currentTimezone;

    // Init timezone options
    $this->data['timezone_options'] = '';
    // Loop through timezones
    foreach ($this->getTimezoneList() as $value => $description) {
      // Check if selected
      $selected = (!empty($currentTimezone) && trim($currentTimezone) == $value) ? ' selected' : '';

      // Set to output
      $this->data['timezone_options'] .= '<option value="' . $value . '"' . $selected . '>' . $description . '</option>';
    }
  }

  /**
   * Core Settings Page
   */
  public function core($failedSave = FALSE)
  {
    // Check user is allowed to view this
    if (!$this->role->canDo('edit_core_settings')) {
      // Redirect user with error
      flashMsg('admin_settings', '<strong>Error</strong> Sorry, you are not allowed to edit the core site settings. Please contact your site administrator for access to this.', 'warning');
      redirectTo('admin/settings');
      exit;
    }

    // Set Breadcrumbs
    $this->data['breadcrumbs'][] = array(
      'text' => 'Core',
      'href' => get_site_url('admin/settings/core')
    );

    //Check if page posted and process form if it is
    if ($failedSave === TRUE) {

      // If it's made it this far there were errors. Load edit view with data

      // Set error message
      flashMsg('settings_core', '<strong>Error</strong> There was an error updating the core settings. Please try again.', 'danger');
    }

    // Get options
    if ($optionResults = $this->settingModel->getOptions('core')) {

      // Set option data
      foreach ($optionResults as $optionData) {
        $this->data['curr_' . $optionData->option_name] = $optionData->option_value;
        $this->data['set_' . $optionData->option_name] = $optionData->option_value;

        // Check if bool
        if (strlen($optionData->option_value) === 1 && ($optionData->option_value == "0" || $optionData->option_value == "1")) {
          $this->data[$optionData->option_name . '_bool'] = TRUE;
        }
      }

      // Get the timezone options
      $this->getTimezoneOptions($this->data['set_site_timezone']);

      // Load view
      $this->load->view('settings/core', $this->data, 'admin');
      exit;
    } // Failed to get options. Return to main settings page

    // Set error
    flashMsg('admin_settings', '<strong>Error</strong> There was an error loading the core settings. Please try again', 'warning');

    // Redirect
    redirectTo('admin/settings');
    exit;
  }

  /**
   * Mail Settings Page
   */
  public function mail($failedSave = FALSE)
  {
    // Check user is allowed to view this
    if (!$this->role->canDo('edit_mail_settings')) {
      // Redirect user with error
      flashMsg('admin_settings', '<strong>Error</strong> Sorry, you are not allowed to edit the mail settings. Please contact your site administrator for access to this.', 'warning');
      redirectTo('admin/settings');
      exit;
    }

    // Set Breadcrumbs
    $this->data['breadcrumbs'][] = array(
      'text' => 'Mail',
      'href' => get_site_url('admin/settings/mail')
    );

    //Check if page posted and process form if it is
    if ($failedSave === TRUE) {

      // If it's made it this far there were errors. Load edit view with data

      // Set error message
      flashMsg('settings_mail', '<strong>Error</strong> There was an error updating the mail settings. Please try again.', 'danger');
    }

    // Get options
    if ($optionResults = $this->settingModel->getOptions('mail')) {

      // Set option data
      foreach ($optionResults as $optionData) {
        $this->data['curr_' . $optionData->option_name] = $optionData->option_value;
        $this->data['set_' . $optionData->option_name] = $optionData->option_value;

        // Check if bool
        if (strlen($optionData->option_value) === 1 && ($optionData->option_value == "0" || $optionData->option_value == "1")) {
          $this->data[$optionData->option_name . '_bool'] = TRUE;
        }
      }

      // Load view
      $this->load->view('settings/mail', $this->data, 'admin');
      exit;
    } // Failed to get options. Return to main settings page

    // Set error
    flashMsg('admin_settings', '<strong>Error</strong> There was an error loading the mail settings. Please try again', 'warning');

    // Redirect
    redirectTo('admin/settings');
    exit;
  }

  /**
   * Security Settings Page
   */
  public function security($failedSave = FALSE)
  {
    // Check user is allowed to view this
    if (!$this->role->canDo('edit_security_settings')) {
      // Redirect user with error
      flashMsg('admin_settings', '<strong>Error</strong> Sorry, you are not allowed to edit the security settings. Please contact your site administrator for access to this.', 'warning');
      redirectTo('admin/settings');
      exit;
    }

    // Set Breadcrumbs
    $this->data['breadcrumbs'][] = array(
      'text' => 'Security',
      'href' => get_site_url('admin/settings/security')
    );

    //Check if page posted and process form if it is
    if ($failedSave === TRUE) {

      // If it's made it this far there were errors. Load edit view with data

      // Set error message
      flashMsg('settings_security', '<strong>Error</strong> There was an error updating the security settings. Please try again.', 'danger');
    }

    // Get options
    if ($optionResults = $this->settingModel->getOptions('security')) {

      // Set option data
      foreach ($optionResults as $optionData) {
        $this->data['curr_' . $optionData->option_name] = $optionData->option_value;
        $this->data['set_' . $optionData->option_name] = $optionData->option_value;

        // Check if bool
        if (strlen($optionData->option_value) === 1 && ($optionData->option_value == "0" || $optionData->option_value == "1")) {
          $this->data[$optionData->option_name . '_bool'] = TRUE;
        }
      }

      // Load view
      $this->load->view('settings/security', $this->data, 'admin');
      exit;
    } // Failed to get options. Return to main settings page

    // Set error
    flashMsg('admin_settings', '<strong>Error</strong> There was an error loading the security settings. Please try again', 'warning');

    // Redirect
    redirectTo('admin/settings');
    exit;
  }

  /**
   * Site Settings Page
   */
  public function site($failedSave = FALSE)
  {
    // Check user is allowed to view this
    if (!$this->role->canDo('edit_site_settings')) {
      // Redirect user with error
      flashMsg('admin_settings', '<strong>Error</strong> Sorry, you are not allowed to edit the site settings. Please contact your site administrator for access to this.', 'warning');
      redirectTo('admin/settings');
      exit;
    }

    // Set Breadcrumbs
    $this->data['breadcrumbs'][] = array(
      'text' => 'Site',
      'href' => get_site_url('admin/settings/site')
    );

    //Check if page posted and process form if it is
    if ($failedSave === TRUE) {

      // If it's made it this far there were errors. Load edit view with data

      // Set error message
      flashMsg('settings_site', '<strong>Error</strong> There was an error updating the site settings. Please try again.', 'danger');
    }

    // Get options
    if ($optionResults = $this->settingModel->getOptions('site')) {

      // Set option data
      foreach ($optionResults as $optionData) {
        $this->data['curr_' . $optionData->option_name] = $optionData->option_value;
        $this->data['set_' . $optionData->option_name] = $optionData->option_value;

        // Check if bool
        if (strlen($optionData->option_value) === 1 && ($optionData->option_value == "0" || $optionData->option_value == "1")) {
          $this->data[$optionData->option_name . '_bool'] = TRUE;
        }
      }

      // Load view
      $this->load->view('settings/site', $this->data, 'admin');
      exit;
    } // Failed to get options. Return to main settings page

    // Set error
    flashMsg('admin_settings', '<strong>Error</strong> There was an error loading the site settings. Please try again', 'warning');

    // Redirect
    redirectTo('admin/settings');
    exit;
  }

  /**
   * Add-on Settings Page
   */
  public function addons($failedSave = FALSE)
  {
    // Check user is allowed to view this
    if (!$this->role->canDo('edit_addon_settings')) {
      // Redirect user with error
      flashMsg('admin_settings', '<strong>Error</strong> Sorry, you are not allowed to edit the add-on settings. Please contact your site administrator for access to this.', 'warning');
      redirectTo('admin/settings');
      exit;
    }

    // Set Breadcrumbs
    $this->data['breadcrumbs'][] = array(
      'text' => 'Add-ons',
      'href' => get_site_url('admin/settings/add-ons')
    );

    //Check if page posted and process form if it is
    if ($failedSave === TRUE) {

      // If it's made it this far there were errors. Load edit view with data

      // Set error message
      flashMsg('settings_site', '<strong>Error</strong> There was an error updating the add-on settings. Please try again.', 'danger');
    }

    // Get options
    if ($optionResults = $this->settingModel->getOptions('addon')) {

      // Set option data
      foreach ($optionResults as $optionData) {
        $this->data['curr_' . $optionData->option_name] = $optionData->option_value;
        $this->data['set_' . $optionData->option_name] = $optionData->option_value;

        // Check if bool
        if (strlen($optionData->option_value) === 1 && ($optionData->option_value == "0" || $optionData->option_value == "1")) {
          $this->data[$optionData->option_name . '_bool'] = TRUE;
        }
      }

      // Load view
      $this->load->view('settings/addon', $this->data, 'admin');
      exit;
    } // Failed to get options. Return to main settings page

    // Set error
    flashMsg('admin_settings', '<strong>Error</strong> There was an error loading the add-on settings. Please try again', 'warning');

    // Redirect
    redirectTo('admin/settings');
    exit;
  }

  /**
   * Logs Page
   *
   * @param mixed $params Mixed params where required
   */
  public function logs(...$params)
  {
    // Check user is allowed to view this
    if (!$this->role->canDo('view_log_settings')) {
      // Redirect user with error
      flashMsg('admin_settings', '<strong>Error</strong> Sorry, you are not allowed to view the log settings. Please contact your site administrator for access to this.', 'warning');
      redirectTo('admin/settings');
      exit;
    }

    // Set Breadcrumbs
    $this->data['breadcrumbs'][] = array(
      'text' => 'Logs',
      'href' => get_site_url('admin/settings/logs')
    );

    // Set the log directory
    $logDirectory = DIR_SYSTEM . "storage" . _DS . "logs" . _DS;

    // Check if any params set
    if (!empty($params[0]) && $params[0] == 'delete' && !empty($params[1])) {

      // Set the file
      $fileNameToDelete = htmlspecialchars(trim($params[1]));

      // Check the file exists
      if (file_exists($logDirectory . $fileNameToDelete . '.log')) {
        // Remove the file
        if (file_put_contents($logDirectory . $fileNameToDelete . '.log', '') !== FALSE) {
          // Set message
          flashMsg('settings_logs', '<strong>Success</strong> The ' . $fileNameToDelete . ' log was emptied.');
        } else { // Couldn't delete the file. Set error
          // Set error
          flashMsg('settings_logs', '<strong>Error</strong> There was an error deleting the emptying log ' . $fileNameToDelete, 'danger');
        }
      } else { // File doesn't exist. Set error
        // Set error
        flashMsg('settings_logs', '<strong>Error</strong> There was an error finding the requested log ' . $fileNameToDelete, 'danger');
      }
      redirectTo('admin/settings/logs');
      exit;
    }

    // Get the directory files
    $fileList = array_diff(scandir(
      $logDirectory
    ), array('..', '.'));

    // Init log output
    $this->data['log_output'] = '';

    // Check if there are any files
    if (!empty($fileList)) {
      // Loop through file list
      foreach ($fileList as $file) {
        // Double check the file is a valid log file
        if (strpos($file, ".log") !== FALSE && file_exists($logDirectory . $file) && !is_dir($logDirectory . $file)) {
          // Set the file name
          $fileName = str_replace('.log', '', $file);
          // Get the file contents to output
          $fileContents = file_get_contents($logDirectory . $file);
          // Check the contents aren't empty
          if (!empty($fileContents)) {
            // Output contents
            $this->data['log_output'] .= '<section class="csc-container cs-mb-3 cs-p-3">
            <h2>' . ucwords($fileName) . ' Log <small><i class="fas fa-trash-alt"></i> <a href="' . get_site_url('admin/settings/logs/delete/' . $fileName) . '">empty the ' . $fileName . ' log</a></small></h2>
            <div class="csc-row">
              <div class="csc-col csc-col12 csc-input-field">
                <textarea disabled>' . $fileContents . '</textarea>
              </div>
            </div>
          </section>';
          }
        }
      }
    }

    // Check if the output is empty
    $this->data['log_output'] = (empty($this->data['log_output'])) ? '<section class="csc-container cs-mb-3 cs-p-3" style="min-height: 0;"><p class="cs-body2">There are no logs to display</p></section>' : $this->data['log_output'];

    // Load view
    $this->load->view('settings/logs', $this->data, 'admin');
    exit;
  }

  /**
   * PHP Info Page
   */
  public function php_info()
  {
    // Check user is allowed to view this
    if (!$this->role->canDo('view_php_info')) {
      // Redirect user with error
      flashMsg('admin_settings', '<strong>Error</strong> Sorry, you are not allowed to view the PHP info. Please contact your site administrator for access to this.', 'warning');
      redirectTo('admin/settings');
      exit;
    }
    phpinfo();
    exit;
  }
}

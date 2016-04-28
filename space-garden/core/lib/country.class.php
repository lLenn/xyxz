<?php

class Country 
{

	public static function retrieve_important_countries()
	{
		return array(
			'BE' => 'Belgium',
			'FR' => 'France',
			'DE' => 'Germany',
			'LU' => 'Luxembourg',
			'NL' => 'Netherlands',
			'PL' => 'Poland',
			'R1' => 'Space-garden');
	}
	
    private function retrieve_countries()
    { 
    	return array(
	        'af' => 'Afganistan',
	        'al' => 'Albania',
	        'dz' => 'Algeria',
	        'as' => 'American Samoa',
	        'ad' => 'Andorra',
	        'ao' => 'Angola',
	        'ai' => 'Anguilla',
	        'aq' => 'Antarctica',
	        'ag' => 'Antigua and Barbuda',
	        'ar' => 'Argentina',
	        'am' => 'Armenia',
	        'aw' => 'Aruba',
	        'au' => 'Australia',
	        'at' => 'Austria',
	        'az' => 'Azerbaijan',
	        'bs' => 'Bahamas',
	        'bh' => 'Bahrain',
	        'bd' => 'Bangladesh',
	        'bb' => 'Barbados',
	        'by' => 'Belarus',
	        'be' => 'Belgium',
	        'bz' => 'Belize',
	        'bj' => 'Benin',
	        'bm' => 'Bermuda',
	        'bt' => 'Bhutan',
	        'bo' => 'Bolivia',
	        'ba' => 'Bosnia and Herzegowina',
	        'bw' => 'Botswana',
	        'bv' => 'Bouvet Island',
	        'br' => 'Brazil',
	        'io' => 'British Indian Ocean Territory',
	        'bn' => 'Brunei Darussalam',
	        'bg' => 'Bulgaria',
	        'bf' => 'Burkina Faso',
	        'bi' => 'Burundi',
	        'kh' => 'Cambodia',
	        'cm' => 'Cameroon',
	        'ca' => 'Canada',
	        'cv' => 'Cape Verde',
	        'ky' => 'Cayman Islands',
	        'cf' => 'Central African Republic',
	        'td' => 'Chad',
	        'cl' => 'Chile',
	        'cn' => 'China',
	        'cx' => 'Christmas Island',
	        'cc' => 'Cocos Keeling Islands',
	        'co' => 'Colombia',
	        'km' => 'Comoros',
	        'cg' => 'Congo',
	        'cd' => 'Congo, Democratic Republic of the',
	        'ck' => 'Cook Islands',
	        'cr' => 'Costa Rica',
	        'ci' => 'Cote d\'Ivoire',
	        'hr' => 'Croatia Hrvatska',
	        'cu' => 'Cuba',
	        'cy' => 'Cyprus',
	        'cz' => 'Czech Republic',
	        'dk' => 'Denmark',
	        'dj' => 'Djibouti',
	        'dm' => 'Dominica',
	        'do' => 'Dominican Republic',
	        'tp' => 'East Timor',
	        'ec' => 'Ecuador',
	        'eg' => 'Egypt',
	        'sv' => 'El Salvador',
	        'gq' => 'Equatorial Guinea',
	        'er' => 'Eritrea',
	        'ee' => 'Estonia',
	        'et' => 'Ethiopia',
	        'fk' => 'Falkland Islands Malvinas',
	        'fo' => 'Faroe Islands',
	        'fj' => 'Fiji',
	        'fi' => 'Finland',
	        'fr' => 'France',
	        'fx' => 'France, Metropolitan',
	        'gf' => 'French Guiana',
	        'pf' => 'French Polynesia',
	        'tf' => 'French Southern Territories',
	        'ga' => 'Gabon',
	        'gm' => 'Gambia',
	        'ge' => 'Georgia',
	        'de' => 'Germany',
	        'gh' => 'Ghana',
	        'gi' => 'Gibraltar',
	        'gr' => 'Greece',
	        'gl' => 'Greenland',
	        'gd' => 'Grenada',
	        'gp' => 'Guadeloupe',
	        'gu' => 'Guam',
	        'gt' => 'Guatemala',
	        'gn' => 'Guinea',
	        'gw' => 'Guinea-Bissau',
	        'gy' => 'Guyana',
	        'ht' => 'Haiti',
	        'hm' => 'Heard and Mc Donald Islands',
	        'va' => 'Holy See (Vatican City State)',
	        'hn' => 'Honduras',
	        'hk' => 'Hong Kong',
	        'hu' => 'Hungary',
	        'is' => 'Iceland',
	        'in' => 'India',
	        'id' => 'Indonesia',
	        'ir' => 'Iran, Islamic Republic of',
	        'iq' => 'Iraq',
	        'ie' => 'Ireland',
	        'il' => 'Israel',
	        'it' => 'Italy',
	        'hm' => 'Jamaica',
	        'jp' => 'Japan',
	        'jo' => 'Jordan',
	        'kz' => 'Kazakhstan',
	        'ke' => 'Kenya',
	        'ki' => 'Kiribati',
	        'kp' => 'Korea, Democratic People\'s Republic of',
	        'kr' => 'Korea, Republic of',
	        'kw' => 'Kuwait',
	        'kg' => 'Kyrgyzstan',
	        'la' => 'Lao People\'s Democratic Republic',
	        'lv' => 'Latvia',
	        'lb' => 'Lebanon',
	        'ls' => 'Lesotho',
	        'lr' => 'Liberia',
	        'ly' => 'Libyan Arab Jamahiriya',
	        'li' => 'Liechtenstein',
	        'lt' => 'Lithuania',
	        'lu' => 'Luxembourg',
	        'mo' => 'Macau',
	        'mk' => 'Macedonia, The Former Yugoslav Republic of',
	        'mg' => 'Madagascar',
	        'mw' => 'Malawi',
	        'my' => 'Malaysia',
	        'mv' => 'Maldives',
	        'ml' => 'Mali',
	        'mt' => 'Malta',
	        'mh' => 'Marshall Islands',
	        'mq' => 'Martinique',
	        'mr' => 'Mauritania',
	        'mu' => 'Mauritius',
	        'yt' => 'Mayotte',
	        'mx' => 'Mexico',
	        'fm' => 'Micronesia, Federated States of',
	        'md' => 'Moldova, Republic of',
	        'mc' => 'Monaco',
	        'mn' => 'Mongolia',
	        'ms' => 'Montserrat',
	        'ma' => 'Morocco',
	        'mz' => 'Mozambique',
	        'mm' => 'Myanmar',
	        'na' => 'Namibia',
	        'nr' => 'Nauru',
	        'np' => 'Nepal',
	        'nl' => 'Netherlands',
	        'an' => 'Netherlands Antilles',
	        'nc' => 'New Caledonia',
	        'nz' => 'New Zealand',
	        'ni' => 'Nicaragua',
	        'ne' => 'Niger',
	        'ng' => 'Nigeria',
	        'nu' => 'Niue',
	        'nf' => 'Norfolk Island',
	        'mp' => 'Northern Mariana Islands',
	        'no' => 'Norway',
	        'om' => 'Oman',
	        'pk' => 'Pakistan',
	        'pw' => 'Palau',
	        'pa' => 'Panama',
	        'pg' => 'Papua New Guinea',
	        'py' => 'Paraguay',
	        'pe' => 'Peru',
	        'ph' => 'Philippines',
	        'pn' => 'Pitcairn',
	        'pl' => 'Poland',
	        'pt' => 'Portugal',
	        'pr' => 'Puerto Rico',
	        'qa' => 'Qatar',
	        're' => 'Reunion',
	        'ro' => 'Romania',
	        'ru' => 'Russian Federation',
	        'rw' => 'Rwanda',
	        'kn' => 'Saint Kitts and Nevis',
	        'lc' => 'Saint LUCIA',
	        'vc' => 'Saint Vincent and the Grenadines',
	        'ws' => 'Samoa',
	        'sm' => 'San Marino',
	        'st' => 'Sao Tome and Principe',
	        'sa' => 'Saudi Arabia',
	        'sn' => 'Senegal',
	        'sc' => 'Seychelles',
	        'sl' => 'Sierra Leone',
	        'sg' => 'Singapore',
	        'sk' => 'Slovakia (Slovak Republic)',
	        'si' => 'Slovenia',
	        'sb' => 'Solomon Islands',
	        'so' => 'Somalia',
	        'za' => 'South Africa',
	        'gs' => 'South Georgia and the South Sandwich Islands',
	        'es' => 'Spain',
	        'lk' => 'Sri Lanka',
	        'sh' => 'St. Helena',
	        'pm' => 'St. Pierre and Miquelon',
	        'sd' => 'Sudan',
	        'sr' => 'Suriname',
	        'sj' => 'Svalbard and Jan Mayen Islands',
	        'sz' => 'Swaziland',
	        'se' => 'Sweden',
	        'ch' => 'Switzerland',
	        'sy' => 'Syrian Arab Republic',
	        'tw' => 'Taiwan, Province of China',
	        'tj' => 'Tajikistan',
	        'tz' => 'Tanzania, United Republic of',
	        'th' => 'Thailand',
	        'tg' => 'Togo',
	        'tk' => 'Tokelau',
	        'to' => 'Tonga',
	        'tt' => 'Trinidad and Tobago',
	        'tn' => 'Tunisia',
	        'tr' => 'Turkey',
	        'tm' => 'Turkmenistan',
	        'tc' => 'Turks and Caicos Islands',
	        'tv' => 'Tuvalu',
	        'ug' => 'Uganda',
	        'ua' => 'Ukraine',
	        'ae' => 'United Arab Emirates',
	        'gb' => 'United Kingdom',
	        'us' => 'United States',
	        'um' => 'United States Minor Outlying Islands',
	        'uy' => 'Uruguay',
	        'uz' => 'Uzbekistan',
	        'vu' => 'Vanuatu',
	        've' => 'Venezuela',
	        'vn' => 'Viet Nam',
	        'vg' => 'Virgin Islands (British)',
	        'vi' => 'Virgin Islands (U.S.)',
	        'wf' => 'Wallis and Futuna Islands',
	        'eh' => 'Western Sahara',
	        'ye' => 'Yemen',
	        'yu' => 'Yugoslavia',
	        'zm' => 'Zambia',
	        'zw' => 'Zimbabwe'
    	);
    }

    /**
     * Finds Country Code
     *
     * @return string|null
     */
    private function find_country_code() 
    {
        $langHead = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
        $langs = preg_split('/\s*,\s*/i', $langHead, -1, PREG_SPLIT_NO_EMPTY);
        $out = "";
        $i = 0;
        $weightIndex = 1;
        foreach ($langs as $lang) 
        {
            $opts = preg_split('/\s*;\s*/i', $lang, -1, PREG_SPLIT_NO_EMPTY);
            $code = $opts[0];
            $codeSegs = explode('-', $code);
            if (array_key_exists(1, $codeSegs)) 
            {
                $out = strtolower($codeSegs[1]);
                break;
            }
        }
        return $out;
    }

    public static function render_country_select($field_name, $selected = null, $first_option = null) 
    {
        if ($selected === null || !array_key_exists($selected, self::retrieve_countries())) 
        {
			$selected = strtolower(Setting::get_instance()->get_default_setting("country"));
        }
    	
    	$html = array();
    	$html[] = "<select name='" . $field_name . "' style='width: 140px'>";
    	if(!is_null($first_option))
			$html[] = '<option value="0">' . $first_option . '</option>';
    	foreach(self::retrieve_countries() as $code => $country)
    	{
			$str = '<option value="'.$code.'"';
			if($selected == $code) $str .= "selected='selected'";
			$str .= ">".$country."</option>";
			$html[] = $str;
    	}
		$html[] = '</select>';
        return implode("\n", $html);
    }

}
?> 
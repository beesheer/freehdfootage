<?php
/**
 * Common functions. Put all the commonly used function here.
 */
class Functions_Common
{
    /**
     * A temp cache
     *
     * @var mixed
     */
    public static $tempCache = null;

    /**
 	 * Formatted day string.
 	 *
 	 * @param mixed $dateString
 	 * @return string
 	 */
	public static function formattedDay($dateString, $format = 'F j, Y')
	{
		$time = strtotime($dateString);
		if ($time) {
			return self::formattedTime($time, $format);
		}
		return $dateString;
	}

	/**
 	 * Formatted day string for a timestamp.
 	 *
 	 * @param integer $time
 	 * @return string
 	 */
	public static function formattedTime($time, $format = 'F j, Y')
	{
		if ($time) {
			$time = date($format, $time);
		}
		return $time;
	}

    /**
     * Formatted percent integer rounded.
     *
     * @params int $num_amount, $num_total
     * @return rounded int
     */
    public static function formattedPercent($num_amount, $num_total)
    {
        if( $num_total==0 ) return 0;
        $count1 = $num_amount / $num_total;
        $count2 = $count1 * 100;
        $count = number_format($count2, 0);
        return $count;
    }

    /**
     * Get a list of dates based on the date ranage and repeat properties.
     *
     * For example, from 2015-01-10 to 2015-01-15, repeat unit is day, repeat number is 2,
     * the list will be 2015-01-10, 2015-01-12, 2015-01-14
     *
     * @param string $startDate
     * @param string $endDate
     * @param string $repeatUnit (day, weak, month)
     * @param integer $repeatNumber
     * @param string $format
     * @param array $exclusions
     * @param string $limitStartDate (even though we have a start point date, this will remove those older than the limit start date)
     * @param string $limitEndDate (even though we have a end point date, this will remove those newer than the limit end date)
     * @return array A list of date following the criteria
     */
    public static function getRepeatedDates($startDate, $endDate, $repeatUnit, $repeatNumber, $format = 'Y-m-d', $exclusions = array(), $limitStartDate, $limitEndDate)
    {
        $dates = array();
        $startDate = new DateTime($startDate);
        $limitStartDate = new DateTime($limitStartDate);
        $limitEndDate = new DateTime($limitEndDate);
        if ($startDate >= $limitStartDate) {
            $dates[] = $startDate->format($format);
        }
        $endDate = new DateTime($endDate);
        $repeatFormatNormalized = '';
        switch ($repeatUnit) {
            case 'day':
            case 'Day':
                $repeatUnitNormalized = $repeatNumber . 'D';
                break;
            case 'week':
            case 'Week':
                $repeatUnitNormalized = $repeatNumber . 'W';
                break;
            case 'month':
            case 'Month':
                $repeatUnitNormalized = $repeatNumber . 'M';
                break;
            default:
                throw new Exception('Invalid repeat unit ' . $repeatUnit . '. Expected: day, week, month.');
                break;
        }
        while($startDate <= $endDate) {
            $startDate->add(new DateInterval('P' . $repeatUnitNormalized));
            if ($startDate <= $endDate && $startDate >= $limitStartDate && $startDate <= $limitEndDate) {
                if (!in_array($startDate->format($format), $exclusions)) {
                    $dates[] = $startDate->format($format);
                }
            }
        }
        return $dates;
    }

    /**
     * Host protocal.
     *
     * @return string
     */
    public static function hostProtocal()
    {
        return 'http' . (isset($_SERVER['HTTPS']) ? 's' : '');
    }

	/**
 	 * Current host url.
 	 *
 	 * @return string
 	 */
	public static function hostUrl()
	{
        return self::hostProtocal() . '://' . self::serverName();
	}

	/**
 	 * Get the correct server name even if for a proxy server behind the firewall.
 	 *
 	 * @return string
 	 *
 	 */
	public static function serverName()
	{
		$domain = $_SERVER['SERVER_NAME'];
		if ($domain == 'localhost' && isset($_SERVER['HTTP_X_FORWARDED_SERVER'])) {
			$domain = $_SERVER['HTTP_X_FORWARDED_SERVER'];
		}
		return $domain;
	}

    /**
     * Limit word count.
     *
     * @param mixed $string
     * @param mixed $count
     * @return string
     */
    public static function limitWordCount($string, $count)
    {
        if (strlen($string) <= $count) {
            return $string;
        }
        $strings = explode('<br />', wordwrap($string, $count, '<br />'));
        return $strings[0];
    }

    /**
     * Get language source file path.
     *
     * @return string
     */
    public static function getCurrentLanguageFile()
    {
        return APPLICATION_PATH . 'languages' . DS . Zend_Registry::getInstance()->locale . '.csv';
    }

    /**
     * Get locale language file.
     *
     * @param string $locale
     * @return string
     */
    public static function getLanguageFile($locale)
    {
        return APPLICATION_PATH . 'languages' . DS . $locale . '.csv';
    }

    /**
     * Client ip.
     *
     * @return string
     */
    public static function clientIp()
    {
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            return $_SERVER['REMOTE_ADDR'];
        }
    }

    /**
     * Preformatted debugging messages.
     *
     * @return void
     */
    public static function pre($array, $title = false)
    {
        if ($title) {
            print '<h3>' . $title . '</h3>';
        }
        print '<pre>' . print_r($array, 1) . '</pre><br />';
    }

    /**
     * Flatten an hierarchical tree into a flat array.
     *
     * @param array $clientTree
     * @param array $targetArray
     * @param string $idKey
     * @param string $labelKey
     * @param string $childKey
     * @return array
     */
    public static function flattenOptionTree($tree, &$targetArray, $idKey = 'id', $labelKey = 'name', $childKey = 'children', $indent = ' - ')
    {
        foreach ($tree as $_leaf) {
            $targetArray[$_leaf[$idKey]] = $indent . $_leaf[$labelKey];
            if (isset($_leaf[$childKey]) && is_array($_leaf[$childKey]) && !empty($_leaf[$childKey])) {
                self::flattenOptionTree($_leaf[$childKey], $targetArray, $idKey, $labelKey, $childKey, $indent . ' - ');
            }
        }
    }

    /**
     * Make sure all the elements in an array is number.
     *
     * @param array $a
     * @return array
     */
    public static function arrayOfNumbers($a)
    {
        // Make sure all the ids are numbers;
        array_walk($a, function(&$value){$value = (int)$value;});
        return $a;
    }

    /**
     * Send an email with a list of attachments
     *
     * @param mixed $toEmail
     * @param mixed $subject
     * @param mixed $message
     * @param mixed $fromEmail
     * @param mixed $attachments
     */
    public static function sendEmailWithAttachments($toEmail, $subject, $message, $fromEmail = false, $attachments = array(), $ccEmail = false)
    {
        // We allow mutiple emails
        $emails = explode(',', $toEmail);
        $ccEmails = "";
        if(!empty($ccEmail))
        {
            $ccEmails = explode(',', $ccEmail);
        }

        if (empty($emails)) {
            return false;
        }
        // Email with template
        
            $emailBody = $message ? $message : '';
            $emailSubject = $subject ? $subject : '';
            $emailAgent = new Mail_Mail('simple_text', $fromEmail ? false : true);
            $emailAgent->setBody($emailBody)
                ->setSubject($emailSubject);
            if ($fromEmail) {
                
                $fromName = $fromEmail;

                $userObj = Repo_User::getInstance();
                $user_id = $userObj->emailExists($fromEmail);                

                $_user = new Object_User($user_id);                
                if (!$_user->getId()) {
                    continue;
                }
                $userInfoArr = $_user->getBasicInfo();                                
                
                if(!empty($userInfoArr["firstname"]) || !empty($userInfoArr["surname"]))
                {
                    $fromName = trim($userInfoArr["firstname"]." ".$userInfoArr["surname"]);
                }                
                $emailAgent->setFrom($fromName, $fromEmail);
            }
            
            if (!empty($attachments)) {
                foreach ($attachments as $_a) {
                    // Add attachment
                    $emailAgent->addAttachment($_a['path'], $_a['name']);
                }
            }

        if(!empty($ccEmail))
        {
            foreach ($ccEmails as $_ccEmail) {
                $emailAgent->setCc($_ccEmail, $_ccEmail);
            }   
        }

        foreach ($emails as $_toEmail) {
            $emailAgent->setTo($_toEmail);
        }        
            try {
                $emailAgent->send();
            } catch (Exception $e) {
                // Ignore for now
            }

        return true;
    }
}
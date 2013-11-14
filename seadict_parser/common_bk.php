<?php
    /*********************************************************************************
    * Common Classes, Constants and Functions for Pageworth 
    * Shared between all Pageworth programs
    *********************************************************************************/

    class ConstSystem 
    {
        /**
        * Token for OurRef
        */
        const TOKEN_OURREF_BEGIN = "#__";
        const TOKEN_OURREF_END = "__#";    

        //db field placeholder token: used in template for value replacing, the values here are escaping 
        const TOKEN_FIELD_BEGIN = "\[\[";
        const TOKEN_FIELD_END = "\]\]";

        /**
        * Encode file name from ID and created date
        * 
        * @param string $userKey
        * @param string $itemID
        * @param int $itemDateCreated Date string or unix time stamp
        * @return mixed
        */
        public static function encodeFileName($userKey, $itemID, $itemDateCreated)
        {
            $result = "";
            $itemID = trim($itemID);
            //print($userKey . " - " .  $itemID . " -  " . $itemDateCreated . "<Br/>");            
            if (!is_numeric($itemDateCreated)) $itemDateCreated = strtotime($itemDateCreated);            
            $result = StringEncoder::encryptAES($itemID, $userKey);
            $result .= StringEncoder::strhex($itemDateCreated);


            //logFile($userKey . " == " . $itemID . " == " . $itemDateCreated);

            return $result;
        }

        /**
        * Decode file name (used for debugging only)
        * 
        * @param mixed $userKey
        * @param mixed $encodedString
        */
        public static function decodeFileName($userKey, $encodedString)
        {
            $result = "";

            $itemID = substr($encodedString, 0, 88);
            $dateString = substr($encodedString, 88);

            $itemID = StringEncoder::decryptAES($itemID, $userKey);
            $dateString = StringEncoder::hexstr($dateString);
            $dateTimeObj = new DateTime();
            $dateTimeObj->setTimestamp($dateString);
        }
    } 

    /**
    * Constants for table Job
    */
    class ConstJob
    {
        const STATE_NORMAL = 1;       //normal job
        const STATE_DRAFT = 5;      //draft news or event

        const STATUS_START = 10;        
        const STATUS_CLOSE = 50;

        const LEVEL_NORMAL = 1;       //normal level everyone can see the job
        const LEVEL_OWNER = 2;       //owner level only owner can see the job

        const TYPE_1 = 1;
        const TYPE_2 = 2;
        const TYPE_3 = 3;
        const TYPE_4 = 4;
    } 

    /**
    * Constants for table Email
    */
    class ConstEmail
    {
        const STATE_RECEIVED = 1;       //email received by parser
        const STATE_SENT = 2;           //email sent out by pw
        const STATE_DRAFT = 3;          //email is a draft
        const STATE_TEMPLATE = 4;       //email is a template
        const STATE_NOTE = 5;           //email is a note    
        const STATE_REPORT_DRAFT = 6;   //email is a draft report
        const STATE_DELETED = -1;       //email is deleted, this is not really a number in databasees, this number will be multiplied with the email.state to get the final number

        const STATE_PHONECALL_IN = 7; //email is a phone call
        const STATE_PHONECALL_OUT = 8; //email is a phone call
        const STATE_SMS_IN = 9; //email is an SMS
        const STATE_SMS_OUT = 10; //email is an SMS



        const EMAIL_UNREAD = 0;
        const EMAIL_READ = 1;

        //token for db field in template
        const TOKEN_DB_FIELD_BEGIN = "##";
        const TOKEN_DB_FIELD_END = "##";

    }

    /**
    * Constants for table Item
    */
    class ConstItem
    {
        //ItemType
        const ITEMTYPE_JOB = 1000;
        const ITEMTYPE_EMAIL = 2000;
        const ITEMTYPE_SCHEDULE = 3000;

        //States
        const STATE_NORMAL = 1;

        /**
        * get the parentType of the input itemType
        * 
        * @param mixed $itemType
        * @return mixed $parentType
        */
        public static function getParentType($itemType)
        {
            /*
            const ITEMTYPE_JOB = 1000;
            const ITEMTYPE_EMAIL = 2000;
            const ITEMTYPE_SCHEDULE = 3000;
            */
            $parentType = -1;
            $totalItemPerType = 999;

            if($itemType > ConstItem::ITEMTYPE_JOB && $itemType <= ConstItem::ITEMTYPE_JOB + $totalItemPerType)
                $parentType = ConstItem::ITEMTYPE_JOB;
            else if($itemType > ConstItem::ITEMTYPE_EMAIL && $itemType <= ConstItem::ITEMTYPE_EMAIL + $totalItemPerType)
                    $parentType = ConstItem::ITEMTYPE_EMAIL;
                else if($itemType > ConstItem::ITEMTYPE_SCHEDULE && $itemType <= ConstItem::ITEMTYPE_SCHEDULE + $totalItemPerType)
                        $parentType = ConstItem::ITEMTYPE_SCHEDULE;

                    //print("getParentType: " . $parentType);
                    return $parentType;
        } 


        /**
        * Get ItemType value
        * 
        * @param mixed $parentValue value of parent, 1000 for job, 2000 for email ...
        * @param mixed $fileName* 
        */
        public static function ext2ItemType($parentValue, $fileName)
        {
            $arrItemType = array(
            "jpg" => 1,"jpeg" => 2,
            "png" => 3,"bmp" => 4,
            "gif" => 5,"tif" => 6,
            "pdf" => 7,"txt" => 8,
            "doc" => 9,"docx" => 10,
            "rtf" => 11,"htm" => 12,
            "html" => 13,"xls" => 14,
            "xlsx" => 15,"xml" => 16,
            "zip" => 17,"arj" => 18,
            "tml" => 19,"asp" => 20,
            "bin" => 21,"vcf" => 22,
            "msg" => 23,"eml" => 24,
            "dat" => 25,"peg" => 26,
            "ocx" => 27,"nts" => 28,
            "lnk" => 29,"rds" => 30,
            //31 -> 100 are reserved for job


            //100 -> for more new file extension.
            "mov" => 101, "avi"=> 102,
            "mp3" => 103, "mp4" => 104,
            "wma" => 105, "wmv" => 106,
            "vob" => 107, "wav" => 108 ,
            "flv" => 109, "3gp" => 110,
            "4gp" => 111, "rar" => 112,
            "ico" => 113, "chm" => 114,
            "xps" => 115, "ps" => 116,
            "ics" => 117, "url" => 118,
            "snag" => 119, "odt" => 120,
            "ifn" => 121, "db" => 122,
            "gsm" => 123, "apk" => 124,
            "dot" => 125, "csv" => 126,
            "ods" => 127, "7z" => 128,
            "pot" => 129, "pptx" => 130,
            "xps" => 131, "sql" => 132,
            "xsd" => 133, "php" => 134,
            "piz" => 135, "exe" => 136            
            );

            $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            if (isset($arrItemType[$fileExtension]))
            {
                $itemType = $arrItemType[$fileExtension];
            }
            else
            {
                $itemType = 0;
            }
            return $itemType + $parentValue;
        }

        /**
        * itemtype to file extension
        * 
        * @param mixed $parentType value of parent, 1000 for job, 2000 for email ...
        * @param mixed $itemType        
        */
        public static function itemType2Ext($itemType)
        {                   
            if (!is_numeric($itemType))
                $itemType = 999;        

            //get the parentType
            $parentType = ConstItem::getParentType($itemType);

            //sub itemType
            $subItemType= $itemType % $parentType;

            $arrItemType2Ext = array (            
            "1"=>"jpg",
            "2"=>"jpeg",
            "3"=>"png",
            "4"=>"bmp",
            "5"=>"gif",
            "6"=>"tif",
            "7"=>"pdf",
            "8"=>"txt",
            "9"=>"doc",
            "10"=>"docx",
            "11"=>"rtf",
            "12"=>"htm",
            "13"=>"html",
            "14"=>"xls",
            "15"=>"xlsx",
            "16"=>"xml",
            "17"=>"zip",
            "18"=>"arj",
            "19"=>"tml",
            "20"=>"asp",
            "21"=>"bin",
            "22"=>"vcf",
            "23"=>"msg",
            "24"=>"eml",
            "25"=>"dat",
            "26"=>"peg",
            "27"=>"ocx",
            "28"=>"nts",
            "29"=>"lnk",            
            "30"=>"rds",
            //31 -> 100 are reserved for job
            "31"=>"jpg",
            "32"=>"jpg",            
            //31 -> 100 are reserved for job

            //100 -> for more new file extension.
            "101" => "mov",
            "102"=> "avi",
            "103"=> "mp3", 
            "104"=> "mp4" ,
            "105" => "wma" , 
            "106" => "wmv" ,
            "107"=> "vob" ,  
            "108"=> "wav" ,
            "109"=> "flv" , 
            "110"=> "3gp" ,
            "111"=> "4gp" , 
            "112"=> "rar" ,
            "113"=> "ico",
            "114"=> "chm",      
            "115" => "xps" , 
            "116" => "ps" , 
            "117" => "ics" ,
            "118" =>"url" ,
            "119" => "snag" ,
            "120" => "odt" ,
            "121" => "ifn" ,
            "122" => "db" ,
            "123" => "gsm" ,
            "124" => "apk" ,
            "125" => "dot" ,
            "126" => "csv" ,
            "127" => "ods" , 
            "128" => "7z" ,
            "129" => "pot" , 
            "130" => "pptx" , 
            "131" => "xps" ,
            "132" => "sql" ,
            "133" => "xsd" , 
            "134" =>"php" , 
            "135" =>"piz" , 
            "136" => "exe" ,                                  
            "999"=>"unknown"
            );        
            return (array_key_exists($subItemType, $arrItemType2Ext)) ? $arrItemType2Ext[$subItemType] : $arrItemType2Ext[999];
        }    


    }

    /**
    * Constants for table User  
    */
    class ConstUser
    {
        const LEVEL_ADMIN = 1;
        const LEVEL_MANAGER = 2;
        const LEVEL_USER = 4;
        const LEVEL_GUEST = 8;
        const LEVEL_INACTIVE = 16;

        const TYPE_USER = 1;        //a normal user in pageworth system
        const TYPE_ASSESSOR = 2;    //assessor users type: can login and show up in Assessor auto text
        const TYPE_CLAIM_TECH = 4;  //claim tech users type: can login and show up in Claimtech auto text

        const TYPE_ADDRESS_BOOK = 16;//the auto text email for address book or contact lookup        

        /**
        * User created by Parser from emailSender
        */
        const LEVEL_NEW_SENDER = 256;


        const STATE_NORMAL = 1;      
    }


    /**
    * Constants for table aiCompany
    */
    class ConstCompany
    {
        /**
        * Companies using Pageworth system
        */
        const TYPE_PAGEWORTH = 1;

        /**
        * Companies are insurances
        */
        const TYPE_INSURANCE = 2;

        /**
        * Companies are insurances
        */
        const TYPE_BORKER = 4;


        /**
        * Companies are suppliers
        */
        const TYPE_SUPPLIER = 8;

        /**
        * Companies are autotext
        */
        const TYPE_AUTOTEXT = 16;

        /**
        * normal state for companies
        */
        const STATE_NORMAL = 1;      
    }


    /**
    * Functions to encode/decode string
    */
    class StringEncoder
    {

        /**
        * Encrypt a string
        * Best for short data ( <= 64 chars)
        * 
        * @param mixed $sValue
        * @param mixed $sSecretKey
        * @return string
        */
        public static function encryptAES($sValue, $sSecretKey)
        {
            $result = trim(base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $sSecretKey, $sValue, MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND))));
            $result = StringEncoder::strhex($result);
            return $result;
        }

        /**
        * Decrypt a string which was encrypted by encryptAES
        * 
        * @param mixed $sValue
        * @param mixed $sSecretKey
        * @return string
        */
        public static function decryptAES($sValue, $sSecretKey)
        {
            $sValue = StringEncoder::hexstr($sValue);
            $result = trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $sSecretKey, base64_decode($sValue), MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND)));
            return $result;
        }


        /**
        * Paul Gregg <pgregg@pgregg.com>
        * 3 October 2003
        *
        * Open Source Code:   If you use this code on your site for public
        * access (i.e. on the Internet) then you must attribute the author and
        * source web site: http://www.pgregg.com/projects/php/code/hexstr.phps
        *
        */

        /**
        * Hex to string
        * 
        * @param mixed $hexstr
        * @return string
        */
        public static function hexstr($hexstr) {
            $hexstr = str_replace(' ', '', $hexstr);
            $hexstr = str_replace('\x', '', $hexstr);
            $retstr = pack('H*', $hexstr);
            return $retstr;
        }

        /**
        * String to hex (double the length)
        * 
        * @param mixed $string
        * @return mixed
        */
        public static function strhex($string) {
            $hexstr = unpack('H*', $string);
            return array_shift($hexstr);
        }
    }


    /**
    * Constants for table aitime
    */
    class ConstTime
    {
        const STATE_DEFAULT = 0;
        const STATE_DELETED = -1;
        const STATE_PHONE_SMS_IMPORTED = -2;        
    }

    class ConstQuickFill
    {
        const STATE_NORMAL = 1;
    }

    class ConstInvoice
    {
        const STATE_NORMAL = 1;
    }

    class ConstInvoiceItem
    {
        const STATE_NORMAL = 1;

        const FEE_TYPE_HOUR = 1;
        const FEE_TYPE_EXPENSE = 2;
        const FEE_TYPE_KM = 3;
    }

    class ConstCost
    {
        const STATE_DEFAULT = 1;
        const STATE_MANUAL = 2;
        const STATE_TIMER = 3;
    }
?>

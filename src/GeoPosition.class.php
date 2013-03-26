<?php

/* 
 * File:        GeoPosition.class.php
 * Version:     V1.00, Alpha
 * Create-Date: 18.10.2012
 * Last-Edit:   08.11.2012
 * Programmer:  Thomas Pointhuber (pointhi)
 * Licence:     GNU General Public License (GNU GPL v3)
 * Website:     http://www.oe5tpo.com
 * 
 * Description: Convert Geographical Data into/from:
 * 
 *              + Locator System            (e.g. JN67VU) or (e.g. AL45dw89ab)
 *              + Coordinate Decimal        (e.g. +48.548621) or (e.g. 115,486545 E)
 *              + Coordinate Sexagesimal    (e.g. 15°15.52'N) or (e.g. +50°48'13'')
 * 
 */

class GeoPosition {
    
    private     $ClassLatitude;     // Latitude in Decimal-Format
    private     $ClassLongitude;    // Longitude in Decimal-Format

    /*
     * Function:    __construct(...)
     * Status:      Alpha (tested without errors)
     * 
     * Call:        __construct();                      // without parameters, init to null
     *              __construct($locator);              // with 1. parameter, init with locator
     *              __construct($latitude, $lonitude)   // with 2. parameters, init with coordinate
     */
    
    public function __construct($ConstrLatStr = null, $ConstrLon = null)
    {
        $this->Init();
        
        if($ConstrLatStr == null && $ConstrLon == null)   // There are no Parameters handed 
        {
            
        }
        else if($ConstrLatStr != null && $ConstrLon == null)  // It handed a locator
        {
            $this->SetString($ConstrLatStr);
        }
        else    // It handed latitude and longitude
        {
            $this->SetGeographical($ConstrLatStr, $ConstrLon);
        }
    }    
    
    /*
     * Function:    Init()
     * Status:      Alpha (tested without errors)
     */
    
    public function Init()
    {
        $this->ClassLatitude = null;
        $this->ClassLongitude = null;        
    }    
    
    /*
     * Function:    SetGeographical(...)
     * Status:      Alpha (tested without errors)
     */
    
    public function SetGeographical($Latitude, $Longitude)
    {
        $this->ClassLatitude    = $this->ConvertCoordinateToDecimal($Latitude);
        $this->ClassLongitude   = $this->ConvertCoordinateToDecimal($Longitude);
        
        if($this->ClassLatitude != false && $this->ClassLongitude != false)
        {
            return true;    // Return Success
        }
        else
        {
            return false;   // Return Error
        }
    }
    
    /*
     * Function:    SetLocator(...)
     * Status:      Alpha (tested without errors)
     */
    
    public function SetLocator($Locator)
    {
        $Locator        = str_replace(array(' ', '\s','&nbsp;'),null,$Locator);   // remove spaces
        $Locator        = strtolower($Locator);             // to lowercase everything
        $StringLenght   = strlen($Locator);
        
        $Error      = false;
        
        $Longitude  = -180;
        $Latitude   = -90;
        $Factor     = 18/24;
        
        for($i = 0;$i<$StringLenght/2;$i ++)
        {
            if( ($i % 2) == 0)  // even number ... letters
            {   
                
                $Factor *= 24;
                
                if($Locator[$i*2] >= 'a' && $Locator[$i*2] <= 'x')
                {
                    if($i==0 && $Locator[$i*2] > 'r')
                    {
                        $Error = true;
                        break;
                    }
                    
                    $Longitude   += (ord($Locator[$i*2])-ord('a'))*(360/$Factor);
                }
                else
                {
                    $Error = true;
                    break;
                }
                
                if($Locator[($i*2)+1] >= 'a' && $Locator[($i*2)+1] <= 'x')
                {
                    if($i==0 && $Locator[($i*2)+1] > 'r')
                    {
                        $Error = true;
                        break;
                    }
                    $Latitude   += (ord($Locator[($i*2)+1])-ord('a'))*(180/$Factor);
                }
                else
                {
                    $Error = true;
                    break;
                }
            }
            else                // odd number ... number
            {
                $Factor *= 10;

                if($Locator[$i*2] >= '0' && $Locator[$i*2] <= '9')
                {   
                    $Longitude   += (ord($Locator[$i*2])-ord('0'))*(360/$Factor);
                }
                else
                {
                    $Error = true;
                    break;
                }
                
                if($Locator[($i*2)+1] >= '0' && $Locator[($i*2)+1] <= '9')
                {
                    $Latitude   += (ord($Locator[($i*2)+1])-ord('0'))*(180/$Factor);
                } 
                else
                {
                    $Error = true;
                    break;
                }
            }
        }
        $this->ClassLongitude   = doubleval($Longitude);
        $this->ClassLatitude    = doubleval($Latitude);
        
        return(!$Error);    // true ... success
    }

    /*
     * Function:    SetString(...)
     * Status:      pre-Alpha (tested without errors)
     */
    
    public function SetString($String)
    {
        $String = str_replace(',','.',$String);     // convert "," to "."
        //$String = strtolower($String);             // to lowercase everything
        $StringLenght   = strlen($String);
        $String = preg_replace('#°#', '&deg;', $String);
        $String = preg_replace('#(\s|&nbsp;)+#', ' ', $String);
        $String = preg_replace('#^\s|(?<=[0-9])\s(?!([0-9]))|(?<=&deg;|\')\s(?!([0-9]+&deg;)|-|\+)|(?<=-|\+)\s(?=[0-9]+&deg;)|\s$#', null, $String);    // delete all unnecessary spaces
        if($String == preg_replace('#[^0-9a-z\s]#i', null, $String))
        {
            $this->SetLocator($String);
        }
        else
        {
            if(substr_count($String, ' ') == 1)
            {
                $StringArray = explode(' ', $String);
                $this->SetGeographical($StringArray[0],$StringArray[1]);                
            }
        }
    }

    /*
     * Function:    ConvertCoordinateToDecimal(...)
     * Status:      pre-Alpha (tested without errors)
     */
    
    public function ConvertCoordinateToDecimal($Coordinate)
    {
        $Coordinate = str_replace(array(' ', '\s','&nbsp;'),null,$Coordinate);    // remove spaces
        $Coordinate = str_replace(',','.',$Coordinate);     // convert "," to "."
        
        $HelpCoordinate = doubleval($Coordinate);
        
        if($Coordinate == "$HelpCoordinate")   // Coordinate already in decimal Format
        {
            return doubleval($Coordinate);
        }
        else
        {
            if(preg_match('#W|S|-#i', $Coordinate))     // If Negative Coordinates then $multiplicator...-1
            {
                $Multiplicator = -1;
            }
            else
            {
                $Multiplicator = 1;
            }
            
            $Coordinate = preg_replace('#°#', '&deg;', $Coordinate);                // convert all Grad to &deg;
            $Coordinate = preg_replace('#[a-z](?!eg;|g;|;)|[+|-]#i', null, $Coordinate);  // remove all letters and +/- except &deg;

            $DegreesExplode = explode("&deg;",$Coordinate);
            if(count($DegreesExplode)>1)
            {
                $MinutesExplode = explode("'",$DegreesExplode[1]);
                if(count($MinutesExplode)>1)
                {
                    $SecondsExplode = explode("''",$MinutesExplode[1]);
                    $Seconds    = $SecondsExplode[0];
                }
                else
                {
                    $Seconds    = 0;
                }
                
                $Minutes    = $MinutesExplode[0];
            }
            else
            {
                $Minutes    = 0;
                $Seconds    = 0;
            }
            
            $Degrees    = $DegreesExplode[0];

            
            return (($Degrees+($Minutes/60)+($Seconds/(60*60)))*$Multiplicator);
        }
    }    

    /*
     * Function:    ConvertCoordinateToSexagesimal(...)
     * Status:      Alpha (tested without errors)
     */
    
    public function ConvertCoordinateToSexagesimal($Coordinate,$Positive = 'NE',$Negative= 'SW',$Unicode = true,$LeadingZeros = false,$EnableSpace = true)
    {
        $Degrees        = intval($Coordinate);
        $DirectionSign  = $Positive;
        
        if($Coordinate < 0) // If Negative Coordinate
        {
            $Coordinate *= -1;
            $Degrees    *= -1;
            $DirectionSign  = $Negative;
        }
        
        $Minutes    = intval(fmod($Coordinate,1)*60);
        $Seconds    = doubleval(fmod(($Coordinate*60),1)*60);
        
        if($Unicode == true)    // DegreeSign as Unicode or direct?
        {
            $DegreeSign = '&deg;';
            $SpaceSign  = '&nbsp;';
        }
        else
        {
            $DegreeSign = '°';
            $SpaceSign  = ' ';
        }
        
        if($EnableSpace == false)
        {
            $SpaceSign  = null;
        }
        
        if($LeadingZeros == false)
        {
            return ($Degrees.$DegreeSign.$SpaceSign.$Minutes.'\''.$SpaceSign.sprintf("%0.2f",$Seconds).'\'\''.$SpaceSign.$DirectionSign);
        }
        else
        {
            return (sprintf("%03d",$Degrees).$DegreeSign.$SpaceSign.sprintf("%02d",$Minutes).'\''.$SpaceSign.sprintf("%02.2f",$Seconds).'\'\''.$SpaceSign.$DirectionSign);
        }
    }
    
    /*
     * Function:    GetLocator(...)
     * Status:      pre-Alpha (tested without errors)
     */
    
    public function GetLocator($Accuray = 6,$LargeSmall = true)
    {
        $OutputString   = null;
        $Longitude      = $this->ClassLongitude+180;
        $Latitude       = $this->ClassLatitude+90;

        $Factor     = 18/24;
        
        for($i = 0;$i<$Accuray/2;$i ++)
        {
            if( ($i % 2) == 0)  // even number ... letters
            {          
                $Factor *= 24;
               
                if($LargeSmall == true && $i!=0)
                {
                    $OutputString .= chr(intval($Longitude/(360/$Factor))+ord('a'));
                    $OutputString .= chr(intval($Latitude/(180/$Factor))+ord('a'));    
                }
                else
                {    
                    $OutputString .= chr(intval($Longitude/(360/$Factor))+ord('A'));
                    $OutputString .= chr(intval($Latitude/(180/$Factor))+ord('A'));
                }
            }
            else                // odd number ... number
            {
                $Factor *= 10;
                
                $OutputString .= chr(intval($Longitude/(360/$Factor))+ord('0'));
                $OutputString .= chr(intval($Latitude/(180/$Factor))+ord('0'));
            }
            $Longitude  -= intval($Longitude/(360/$Factor))*(360/$Factor);
            $Latitude   -= intval($Latitude/(180/$Factor))*(180/$Factor);
        }
        return $OutputString;
    }

    /*
     * Function:    GetLatitudeDecimal()
     * Status:      Alpha (tested without errors)
     */
    
    public function GetLatitudeDecimal()
    {
        return doubleval(sprintf("%0.6f",$this->ClassLatitude));
    }
    
    /*
     * Function:    GetLongitudeDecimal()
     * Status:      Alpha (tested without errors)
     */
    
    public function GetLongitudeDecimal()
    {
        return doubleval(sprintf("%0.6f",$this->ClassLongitude));
    }

    /*
     * Function:    GetDecimal(...)
     * Status:      Alpha (tested without errors)
     */
    
    public function GetDecimal($Unicode = true)
    {
        $String = $this->GetLatitudeDecimal();
        if($Unicode== true)
        {
            $String .= '&nbsp;';
        }
        else
        {
            $String .= ' ';
        }
        $String .= $this->GetLongitudeDecimal();
        return ($String);
    }
    
    /*
     * Function:    GetLatitudeSexagesimal()
     * Status:      Alpha (tested without errors)
     */
    
    public function GetLatitudeSexagesimal($Unicode = true, $LeadingZeros = false, $EnableSpace = true)
    {
        return $this->ConvertCoordinateToSexagesimal($this->ClassLatitude,'N','S',$Unicode ,$LeadingZeros, $EnableSpace);
    }
    
    /*
     * Function:    GetLongitudeSexagesimal()
     * Status:      Alpha (tested without errors)
     */
    
    public function GetLongitudeSexagesimal($Unicode = true, $LeadingZeros = false, $EnableSpace = true)
    {
        return $this->ConvertCoordinateToSexagesimal($this->ClassLongitude,'E','W',$Unicode ,$LeadingZeros, $EnableSpace);
    }
    
    /*
     * Function:    GetSexagesimal(...)
     * Status:      Alpha (tested without errors)
     */
    
    public function GetSexagesimal($Unicode = true, $LeadingZeros = false)
    {
        $String = $this->GetLatitudeSexagesimal($Unicode, $LeadingZeros, false);
        if($Unicode== true)
        {
            $String .= '&nbsp;';
        }
        else
        {
            $String .= ' ';
        }
        $String .= $this->GetLongitudeSexagesimal($Unicode, $LeadingZeros, false);
        return ($String);
    }
    
}   // End of Class GeoPosition

?>

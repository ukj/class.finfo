<?php

    
    //No special handling.
    if(!defined('FILEINFO_NONE')) define('FILEINFO_NONE',0);
    //Follow symlinks.
    if(!defined('FILEINFO_SYMLINK')) define('FILEINFO_SYMLINK',2);
    //Return the mime type. Available since PHP 5.3.0.
    if(!defined('FILEINFO_MIME_TYPE')) define('FILEINFO_MIME_TYPE',16);
    //Return the mime encoding of the file. Available since PHP 5.3.0.
    if(!defined('FILEINFO_MIME_ENCODING')) define('FILEINFO_MIME_ENCODING',1024);
    //Return the mime type and mime encoding as defined by RFC 2045.
    if(!defined('FILEINFO_MIME')) define('FILEINFO_MIME',1040);
    //Decompress compressed files. Disabled since PHP 5.3.0 due to thread safety issues.
    //if(!defined('FILEINFO_COMPRESS')) define('FILEINFO_COMPRESS',NULL);
    //Look at the contents of blocks or character special devices.
    if(!defined('FILEINFO_DEVICES')) define('FILEINFO_DEVICES',8);
    //Return all matches, not just the first.
    if(!defined('FILEINFO_CONTINUE')) define('FILEINFO_CONTINUE',32);
    //If possible preserve the original access time.
    if(!defined('FILEINFO_PRESERVE_ATIME')) define('FILEINFO_PRESERVE_ATIME',128);
    //Don't translate unprintable characters to a \ooo octal representation.
    if(!defined('FILEINFO_RAW')) define('FILEINFO_RAW',256);
    
     for($this_hx_ascii='',$i=0;$i<128;$i++)$this_hx_ascii .= '\\x'.dechex($i); 
     define('FILEINFO_this_hx_ascii', $this_hx_ascii );
    
    /**
     * This class emulates finfo extensions, it has internal charset detection, 
     *  also it read magic bytes from common file headers. 
     */
    class finfo { //extends finfo {       
     
        /**
         * Magic file path is not really used
         */
        public $magic_file = null;
        /**
         * Flags
         * @var int
         */
        public $flags = 0;
        
        private $hx_ascii='';
        /**
         * Simple log data
         * @var string
         */
        //public $log = '';
        
        /**
         * Modified getid3 magic array
         * 
         * new types: docx odt vcard csv ps rtf js xhtml xml 
         *            xcard ico bzip z exe elf php xslt css
         * 
         * new key: 'fn_extension' => *always* array()
         * new key: 'eval' => string
         * 'inline_attachments' => FALSE | array()
         * 
         * @author getid3-1.9.3
         */
        public $no_magic = array(
        
            // AC-3   - audio      - Dolby AC-3 / Dolby Digital
            'ac3'  => array(
                        'pattern'   => '^\x0B\x77', 
                        'mime_type' => 'audio/ac3',
                        'inline_attachments' => false,
                        'fn_extension'=>array('ac3')
                    ),

            // AAC  - audio       - Advanced Audio Coding (AAC) - ADIF format
            'adif' => array(
                        'pattern'   => '^ADIF', 
                        'mime_type' => 'application/octet-stream',
                        'inline_attachments' => false,
                        'fn_extension'=>array('aac')
                    ),        
        
            // AAC  - audio       - Advanced Audio Coding (AAC) - ADTS format (very similar to MP3)
            'adts' => array(
                        'pattern'   => '^\xFF[\xF0-\xF1\xF8-\xF9]', 
                        'mime_type' => 'application/octet-stream',
                        'inline_attachments' => false,
                        'fn_extension'=>array('aac')
                    ),


            // AU   - audio       - NeXT/Sun AUdio (AU)
            'au'   => array(
                        'pattern'   => '^\.snd', 
                        'mime_type' => 'audio/basic',
                        'inline_attachments' => false,
                        'fn_extension'=>array('au')
                    ),
            // FLAC - audio       - Free Lossless Audio Codec
            'flac' => array(
                        'pattern'   => '^fLaC', 
                        'mime_type' => 'audio/x-flac',
                        'inline_attachments' => false,
                        'fn_extension'=>array('flac')
                    ),             
            // MP3  - audio       - MPEG-audio Layer 3 (very similar to AAC-ADTS)
            'mp3'  => array(
                        'pattern'   => '^\xFF[\xE2-\xE7\xF2-\xF7\xFA-\xFF][\x00-\x0B\x10-\x1B\x20-\x2B\x30-\x3B\x40-\x4B\x50-\x5B\x60-\x6B\x70-\x7B\x80-\x8B\x90-\x9B\xA0-\xAB\xB0-\xBB\xC0-\xCB\xD0-\xDB\xE0-\xEB\xF0-\xFB]', 
                        'mime_type' => 'audio/mpeg',
                        'inline_attachments' => false,
                        'fn_extension'=>array('mp3')
            ),
            // VOC  - audio       - Creative Voice (VOC)
            'voc'  => array(
                        'pattern'   => '^Creative Voice File', 
                        'mime_type' => 'audio/voc',
                        'inline_attachments' => false,
                        'fn_extension'=>array('voc')
            ),
            // ASF  - audio/video - Advanced Streaming Format, Windows Media Video, Windows Media Audio
            'asf'  => array(
                        'pattern'   => '^\x30\x26\xB2\x75\x8E\x66\xCF\x11\xA6\xD9\x00\xAA\x00\x62\xCE\x6C', 
                        'mime_type' => 'video/x-ms-asf',
                        'inline_attachments' => false,
                        'fn_extension'=>array('asf')
            ),
             // FLV  - audio/video - FLash Video
            'flv' => array(
                        'pattern'   => '^FLV\x01', 
                        'mime_type' => 'video/x-flv',
                        'inline_attachments' => false,
                        'fn_extension'=>array('flv')
             ),

            // MKAV - audio/video - Mastroka
            'matroska' => array(
                        'pattern'   => '^\x1A\x45\xDF\xA3', 
                        'mime_type' => 'video/x-matroska', // may also be audio/x-matroska
                        'inline_attachments' => false,
                        'fn_extension'=>array('mka', 'mkav', 'matroska')
             ),

            // MPEG - audio/video - MPEG (Moving Pictures Experts Group)
            'mpeg' => array(
                        'pattern'   => '^\x00\x00\x01(\xBA|\xB3)', 
                        'mime_type' => 'video/mpeg',
                        'inline_attachments' => false,
                        'fn_extension'=>array('mpg','mpeg')
             ),
            // MPEG - audio/video - MPEG (Moving Pictures Experts Group)
            'mpeg4' => array(
                        'pattern'   => 'ftypmp4|isommp4|moov|lmvhd', 
                        'mime_type' => 'video/mp4',
                        'inline_attachments' => false,
                        'fn_extension'=>array('mp4','mpeg4','m4v')
             ),
            // Ogg  - audio/video - Ogg (Ogg-Vorbis, Ogg-FLAC, Speex, Ogg-Theora(*), Ogg-Tarkin(*))
            'ogg'  => array(
                        'pattern'   => '^OggS', 
                        'mime_type' => 'application/ogg',
                        'inline_attachments' => array(
                            'video/ogg'=>'(\x80theora|\x01video)',
                            'audio/ogg'=>'(\x01vorbis|\x7fFLAC|Speex|Xiphophorus|libVorbis)'
                        ),
                        'fn_extension'=>array('ogg')
            ),

            // QT   - audio/video - Quicktime
            'quicktime' => array(
                        'pattern'   => '^.{4}(cmov|free|ftyp|mdat|moov|pnot|skip|wide)', 
                        'mime_type' => 'video/quicktime',
                        'inline_attachments' => false,
                        'fn_extension'=>array('qt')
            ),
            // AVI   - audio/video - Audio Video Interleave
            'avi' => array(
                        'pattern'   => '^.{8}AVI\x20', 
                        'mime_type' => 'video/x-msvideo',
                        'inline_attachments' => false,
                        'fn_extension'=>array('qt')
            ),
            // RIFF - audio/video - Resource Interchange File Format (RIFF) / WAV / AVI / CD-audio / SDSS = renamed variant used by SmartSound QuickTracks (www.smartsound.com) / FORM = Audio Interchange File Format (AIFF)
            'riff' => array(
                        'pattern'   => '^(RIFF|SDSS|FORM|WAVE|RMP3)',
                        'mime_type' => 'audio/x-wave',
                        'inline_attachments' => false,
                        'fn_extension'=>array('wav','riff')
            ),

            // Real - audio/video - RealAudio, RealVideo
            'real' => array(
                        'pattern'   => '^(\\.RMF|\\.ra)', 
                        'mime_type' => 'audio/x-realaudio',
                        'inline_attachments' => false,
                        'fn_extension'=>array('rmf','ra')
             ),

            // SWF - audio/video - ShockWave Flash
            'swf' => array(
                        'pattern'   => '^(F|C)WS', 
                        'mime_type' => 'application/x-shockwave-flash',
                        'inline_attachments' => false,
                        'fn_extension'=>array('swf')
            ),
            // BMP  - still image - Bitmap (Windows, OS/2; uncompressed, RLE8, RLE4)
            'bmp'  => array(
                        'pattern'   => '^BM', 
                        'mime_type' => 'image/bmp',
                        'inline_attachments' => false,
                        'fn_extension'=>array('bmp')
                    ),

            // GIF  - still image - Graphics Interchange Format
            'gif'  => array(
                        'pattern'   => '^GIF', 
                        'mime_type' => 'image/gif',
                        'inline_attachments' => false,
                        'fn_extension'=>array('gif')
                    ),

            // JPEG - still image - Joint Photographic Experts Group (JPEG)
            'jpg'  => array(
                        'pattern'   => '^\xFF\xD8\xFF', 
                        'mime_type' => 'image/jpeg',
                        'inline_attachments' => false,
                        'fn_extension'=>array('jpg','jpeg','jp')
                    ),
            // JP2 JPEG 2000 image
            'jp2' => array(
                        'pattern'      => '^\x00\x00\x00\x0C\x6A\x50\x20\x20\x0D\x0A\x87\x0A|^\xFF\x4F\xFF\x51\x00', 
                        'mime_type'    => 'image/jp2',
                        'inline_attachments' => false,
                        'fn_extension' => array('jp2')
                ),
                
            // PNG  - still image - Portable Network Graphics (PNG)
            'png'  => array(
                        'pattern'   => '^\x89\x50\x4E\x47\x0D\x0A\x1A\x0A',
                        'mime_type' => 'image/png',
                        'inline_attachments' => false,
                        'fn_extension'=>array('png')
                    ),

            // ICO
            'ico' => array(
                        'pattern'      => '^\x00\x00\x01\x00\x03\x00\x10\x10\x00\x00\x00\x00', 
                        'mime_type'    => 'image/vnd.microsoft.icon',
                        'inline_attachments' => false,
                        'fn_extension' => array('ico')
                ),
            // XCF
            'xcf' => array(
                        'pattern'      => '^\x67\x69\x6d\x70\x20\x78\x63\x66\x20\x76', 
                        'mime_type'    => 'image/vnd.gimp.xcf',
                        'inline_attachments' => false,
                        'fn_extension' => array('xcf')
                ),                 
            // SVG  - still image - Scalable Vector Graphics (SVG)
            'svg'  => array(
                        'pattern'   => '(<!DOCTYPE svg PUBLIC |xmlns="http:\/\/www\.w3\.org\/2000\/svg")', 
                        'mime_type' => 'image/svg+xml',
                        'inline_attachments' => false,
                        'fn_extension'=>array('svg')
                    ),


            // TIFF - still image - Tagged Information File Format (TIFF)
            'tiff' => array(
                        'pattern'   => '^(II\x2A\x00|MM\x00\x2A)',
                        'mime_type' => 'image/tiff',
                        'inline_attachments' => false,
                        'fn_extension'=>array('tif', 'tiff')
                    ),


            // EFAX - still image - eFax (TIFF derivative)
            'bmp'  => array(
                        'pattern'   => '^\xDC\xFE',
                        'mime_type' => 'image/efax',
                        'inline_attachments' => false,
                        'fn_extension'=>array('bmp')
                    ),
            // ISO  - data        - International Standards Organization (ISO) CD-ROM Image
            'iso'  => array(
                        'pattern'   => '^.{32769}CD001',
                        'mime_type' => 'application/x-iso-image',
                        'inline_attachments' => false,
                        'fn_extension'=>array('iso')
                    ),
            // DOCX - Office 2007 - ZIP
            'docx' => array(
                        'pattern'      => '[Content_Types].xml', 
                        //'eval'         => 'if( is_int(strpos($head_raw,"[Content_Types].xml")) )$ifPass=TRUE;',
                        'mime_type'    => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                        'inline_attachments' => false,
                        'fn_extension' => array('docx')
                    ),
            // XLSX - Office 2007 - ZIP
            'xlsx' => array(
                        'pattern'      => '^PK&worksheets&[Content_Types].xml', 
                        //'eval'         => 'if( is_int(strpos($head_raw,"[Content_Types].xml")) )$ifPass=TRUE;',
                        'mime_type'    => 'application/vnd.openxmlformats-officedocument.spreadsheetprocessingml.document',
                        'inline_attachments' => false,
                        'fn_extension' => array('xlsx')
                    ),
            // ODT - OpenOffice - ZIP
            'odt' => array(
                        'pattern'      => 'mimetypeapplication/vnd.oasis.opendocument.text', 
                        'mime_type'    => 'application/vnd.oasis.opendocument.text',
                        'inline_attachments' => false,
                        'fn_extension' => array('odt')
                    ),
            // ODS - OpenOffice - ZIP
            'ods' => array(
                        'pattern'      => 'mimetypeapplication/vnd.oasis.opendocument.spreadsheet', 
                        'mime_type'    => 'application/vnd.oasis.opendocument.spreadsheet',
                        'inline_attachments' => false,
                        'fn_extension' => array('ods')
                    ),
            // RAR  - data        - RAR compressed data
            'rar'  => array(
                        'pattern'   => '^Rar\!',
                        'mime_type' => 'application/octet-stream',
                        'inline_attachments' => false,
                        'fn_extension'=>array('rar')
                    ),

            // SZIP - audio/data  - SZIP compressed data
            'szip' => array(
                        'pattern'   => '^SZ\x0A\x04',
                        'mime_type' => 'application/octet-stream',
                        'inline_attachments' => false,
                        'fn_extension'=>array('szip')
                    ),

            // TAR  - data        - TAR compressed data
            'tar'  => array(
                        'pattern'   => '^.{100}[0-9\x20]{7}\x00[0-9\x20]{7}\x00[0-9\x20]{7}\x00[0-9\x20\x00]{12}[0-9\x20\x00]{12}',
                        // TAR (POSIX) ^\x75\x73\x74\x61\x72
                        'mime_type' => 'application/x-tar',
                        'inline_attachments' => false,
                        'fn_extension'=>array('tar')
                    ),

            // GZIP  - data        - GZIP compressed data
            'gz'  => array(
                        'pattern'   => '^\x1f\x9d',
                        'mime_type' => 'application/x-gzip',
                        'inline_attachments' => false,
                        'fn_extension'=>array('gz')
                    ),
            // Bzip  - Compress
            'bz'  => array(
                        'pattern'   => '^\x142\x5a',
                        'mime_type' => 'application/x-bzip',
                        'inline_attachments' => false,
                        'fn_extension'=>array('Z')
                    ),
            // Z  - Compress        - Z compressed data
            'z'  => array(
                        'pattern'   => '^\x1F\x8B\x08',
                        'mime_type' => 'application/x-compress',
                        'inline_attachments' => false,
                        'fn_extension'=>array('Z')
                    ),
            // ZIP  - data         - ZIP compressed data
            'zip'  => array(
                        'pattern'   => '^PK\x03\x04',
                        'mime_type' => 'application/zip',
                        'inline_attachments' => false,
                        'fn_extension'=>array('zip')
                    ),
            // PDF  - data        - Portable Document Format
            'pdf'  => array(
                        'pattern'   => '^\x25PDF',
                        'mime_type' => 'application/pdf',
                        'inline_attachments' => false,
                        'fn_extension'=>array('pdf')
                    ),
            // PS  - Postscript
            'ps'  => array(
                        'pattern'   => '^\x25\x21',
                        'mime_type' => 'application/postscript',
                        'inline_attachments' => false,
                        'fn_extension'=>array('eps','ps','ai')
                    ),
                    
            // CSV
            'csv' => array(
                        'eval'  => 'if( !is_int(strpos($head_raw,"var ")) && ($lttStat[";"]>=$head_ln_cnt) and ($lttStat[":"] < $lttStat[";"] ))$ifPass=TRUE;',
                        'mime_type'     => 'text/csv',
                        'inline_attachments' => false,
                        'fn_extension'  => array('csv')
                ),
                
            // MSOFFICE  - DOC data 
            'doc' => array(
                        'pattern'      => '^\xD0\xCF\x11\xE0\xA1\xB1\x1A\xE1&\x3E\x00\x03\x00\xFE\xFF\x09\x00\x06', 
                        'mime_type'    => 'application/msword',
                        'inline_attachments' => false,
                        'fn_extension' => array('doc')
                    ),
            // MSOFFICE  - XLS data 
            'xls' => array(
                        'pattern'      => '^\xD0\xCF\x11\xE0\xA1\xB1\x1A\xE1&\x3B\x00\x03\x00\xFE\xFF\x09\x00\x06', 
                        'mime_type'    => 'application/vnd.ms-excel',
                        'inline_attachments' => false,
                        'fn_extension' => array('xls')
                    ),
             // RTF
            'rtf' => array(
                        //'eval'      => 'if( is_int(strpos($head_raw,"{\\\rtf"))){$ifPass=TRUE;}',
                        'pattern'      => '^{\\\rtf', 
                        'mime_type'    => 'application/rtf',
                        'inline_attachments'=> false,
                        'fn_extension' => array('rtf')
                ),                
            
            'php'=>array(
                    'pattern'      => '<\\?php|<\\?=',
                    'mime_type'    =>'application/x-httpd-php',
                    'inline_attachments' => false,
                    'fn_extension' => array('php','php3','php5')
                    ),
            'xcard'=> array (
                    'pattern'      => '<vcards',
                    'mime_type'    => 'text/xcard',
                    'inline_attachments' => false,
                    'fn_extension' => array('xcard')
                    ),
            'xhtml'=> array (
                    'pattern'      => '<!DOCTYPE & html',
                    'mime_type'    => 'text/html',
                    'inline_attachments' => false,
                    'fn_extension' => array('htm','html','xhtm','xhtml')
                    ),
            'xml'=> array (
                    'pattern'      => '<!DOCTYPE & xml',
                    'mime_type'    => 'application/xml',
                    'inline_attachments' => false,
                    'fn_extension' => array('xml')
                    ),  
            'xslt'=> array (
                    'pattern'      => '<xsl:stylesheet',
                     // The MIME media types text/xml and application/xml [RFC2376] should be used for XSLT stylesheets. 
                    'mime_type'    => 'application/xslt+xml',
                    'inline_attachments' => false,
                    'fn_extension' => array('xsl')
                    ),  
                            
            'css'=> array (
                    'eval'  => '
                    if( is_int(strpos($head_raw,"width:")) && ($lttStat[":"] > $lttStat["<"]) && ($lttStat[":"] > $lttStat["{"])  )
                    { $ifPass=TRUE; }',
                    'mime_type'    => 'text/css',
                    'inline_attachments' => false,
                    'fn_extension' => array('css')
                    ),

            'js'=> array (
                    'pattern'      => 'var|document.',
                    'mime_type'    => 'text/javascript',
                    'fn_extension' => array('js','javascript','ecmascript'),
                    'inline_attachments' => false
                    ),
            'vcf'=> array (
                    'pattern'      => 'BEGIN:VCARD',
                    'mime_type'    =>'text/vcard',
                    'fn_extension' => array('vcf','vcard'),
                    'inline_attachments' => false
                    ),
                    
            'txt'=> array (
                    // UNICODE BOM? // \x1\x2\x3\x8\x\x9\x\xA\xB\xC\xD\x1A\x1D\x1E\x1F
                    'pattern'      => '[^\x00\xFE\xFF\xDD\x73\xBB\x2B\x2F\xF7\x64\x0E\xFB\xEE\x4C\x6F]{2}[\xFE\xFF\x00\x66\x73\xBF\x76\x4C\x28\xA7\x94\x93]*[FILEINFO_this_hx_ascii]*',
                    'mime_type'    =>'text/plain',
                    'fn_extension' => array('txt','text'),
                    'inline_attachments' => false
                    ),
            'exe'=> array (
                    'pattern'      => '^\x4d\x5a',
                    'mime_type'    => 'application/octet-stream',
                    'inline_attachments' => false,
                    'fn_extension' => array('exe','dll')
                    ),
            'elf'=> array (
                    'pattern'      => '^\x7f\x45\x4c\x46',
                    'mime_type'    => 'application/octet-stream',
                    'inline_attachments' => false,
                    'fn_extension' => array('elf','so','class','lzh','bin','lha','dms')
                    )  
        );
                        
        /**
         * Parsed mime type string
         * @var string
         */
        private $mime = '';
                
        /**
         * Parsed charset from file / finfo
         * @var string
         */
        private $charset_f = ''; 
        

        
        
        
        /**
         * Create a new fileinfo resource
         */
        public function __construct($options=FILEINFO_NONE ,$magic_file = NULL) {
             
            if(is_file($magic_file)) $this->magic_file = $magic_file; else
            foreach(array(
                getenv('MAGIC'), getenv('MAGIC_MIME'),
                '/usr/share/misc/magic', '/usr/share/file/magic', '/usr/share/stegdetect/magic', '/usr/share/file/magic.mime',
                '/Applications/MAMP/conf/apache/magic'
                
            ) as $loc ) if(is_file($loc)) $this->magic_file = $loc;
            
            $this->flags = $options;
            
        }

        /**
         * Close fileinfo resource
         * @return bool
         */
        public function __destruct() { }
        
        /**
         * this function will determine the format of a file based on magic bytes.
         * Returns information about a string buffer.
         * @param string $string
         * @param int $options FILEINFO_NONE
         * @param object $context Unused
         * @return string
         */
        public function buffer(&$string ,$options=FILEINFO_NONE ,$context=NULL) { 
            $this->mime = $this->charset_f = '';
            
            $buf_len = strlen($string);
            $head_raw = rtrim(substr($string, 0, 32774));
            $head = $this->explode_head("\n",$head_raw , 30);
            $head_ln_cnt = count($head);
            
            $lttStat = array();
            foreach(str_split('{}()<>[]=:;.,?!&|~@#"\'*$%+-/\\',1) as $lttStat_k) {
                $lttStatCnt = substr_count($head_raw, $lttStat_k);    
                //if($lttStatCnt>0) 
                $lttStat[$lttStat_k] = $lttStatCnt;
            }
            
            foreach($this->no_magic as $format_name => $info) {
                // The /s switch on preg_match() forces preg_match() NOT to treat
                // newline (0x0A) characters as special chars but do a binary match
                if (!empty($info['pattern']) && preg_match('#'.$info['pattern'].'#s', $string)) {
                    //$info['include'] = 'module.'.$info['group'].'.'.$info['module'].'.php';
                    //if($options & FILEINFO_CONTINUE) ; else return $info;
                    $this->mime = $info['mime_type'];
                    if($this->isTextualData()) {
                        $this->getXMLencoding($string);
                    }
                    
                    if(is_array($info['inline_attachments'])) {
                        foreach($info['inline_attachments'] as $at_mime => $at_pattern) {
                            if( preg_match('#'.$at_pattern.'#s', $head_raw) ) {$this->mime = $at_mime;break;}
                        }
                    }
                    
                    return $this->format_output($options);
                }
                elseif( array_key_exists('eval', $info) ) {
                    $ifPass = FALSE;
                    eval($info['eval']);
                    if( $ifPass===TRUE ) {
                        $this->mime = $info['mime_type'];
                        if($this->isTextualData()) {
                            $this->getXMLencoding($string);
                        }
                        
                        if(is_array($info['inline_attachments'])) {
                            foreach($info['inline_attachments'] as $at_mime => $at_pattern) {
                                if( preg_match('#'.$at_pattern.'#s', $head_raw) )  {$this->mime = $at_mime;break;}
                            }
                        }
                        
                        return $this->format_output($options);
                    }
                }
                
                    
            }
            return FALSE;
            
        }


        /**
         * Return information about a file
         * @return string
         */
        public function file($file_name, $options=FILEINFO_NONE ,$context=NULL) { 
            $this->mime = $this->charset_f = '';
            
            if($options > 0); else $options = $this->flags;
            $flags = '';
            if($options & FILEINFO_SYMLINK) ; else $flags .= '--no-dereference ';
            if($options & FILEINFO_DEVICES) $flags .= '--special-files '; 
            if($options & FILEINFO_CONTINUE) $flags .= '--keep-going ';
            if($options & FILEINFO_PRESERVE_ATIME) $flags .= '--preserve-date ';
            if($options & FILEINFO_RAW) $flags .= '--raw ';
            
            if(is_dir($file_name)) {
                $this->mime = 'inode/directory';
                $this->charset_f = '';
                return $this->format_output($options);
            }
            $buffer = file_get_contents($file_name ,NULL ,NULL ,0 ,32774);
            
            if (strtoupper(substr(PHP_OS, 0, 3)) != 'WIN') 
            {
            
                $e= array();
                $return_var = NULL;
                exec('file --mime --brief '.$flags.'-m "'.$this->magic_file.'" "'.$file_name.'"' ,$e ,$return_var);

                if(is_string(reset($e))){ 
                    $this->parse_fileinfo_mime($e[0], $buffer);
                    return $this->format_output($options);
                }
                
                $e= array(); $exec_i = NULL;
                exec(    'file --mime --brief '.$flags.'-m "'.$this->magic_file.'" "'.$file_name.'"',$e,$exec_i);
                if($exec_i>0) {
                    $e= array(); $exec_i = NULL;
                    exec('file --mime --brief '.$flags.'"'.$file_name.'"' ,$e ,$exec_i);
                } 
                     
                $ec = count($e);
                for($i=0;$i<$ec;$i++){
                    if(strpos($e[$i],'/')) { 
                        $this->parse_fileinfo_mime($e[$i], $buffer); 
                        return $this->format_output($options);
                    }
                }
           }
     

            
            if($options & FILEINFO_SYMLINK) {
                $this->mime = 'inode/symlink';
                $this->charset_f = '';
                return $this->format_output($options); 
            } else {
                if(is_link($file_name)) {
                    $file_name = readlink($file_name);
                }
            }
            
            
            
            //if($fn_ext == '') {
 
                if($result=$this->buffer($buffer ,$options)) {
                    return $result;
                
                }
            //}
            
            $fn_ext = trim( pathinfo(strtolower($file_name), PATHINFO_EXTENSION) );
            // ext to mime: $this->no_magic array keys are rather module names
            foreach($this->no_magic as $module => $info) {
               if(in_array($fn_ext, $info['fn_extension']) ) {
                   $this->charset_f = '';
                   $this->parse_fileinfo_mime($this->no_magic[$module]['mime_type'], $buffer);
                   return $this->format_output($options); 
               }
            }        

            return FALSE;
        }
            
         
        /**
         * Set libmagic configuration options
         * 
         * @param int $options
         */
        public function set_flags($options) {
             $this->flags = $options;
             return TRUE;
        }

        
        /**
         * Returns mime class like "text" in "text/javascript"
         * @return string
         */
        public function get_mime_class() {
            if(is_int($slp = strpos($this->mime,'/'))) {
                return substr($this->mime,0,$slp);
            } return 'unknown';
        }
        
        /**
         * Returns mime sub type class/subtype like "javascript" in "text/javascript"
         * @return string
         */
        public function get_mime_subtype() {
            if(is_int($slp = strpos($this->mime,'/'))) {
                return substr($this->mime,$slp+1);
            } return 'unknown';
        }

                
        /**
         * Returns filename extension correspond current detected or provided mime type
         * Example: If detected file is "text/javascript", method returns "js"
         * 
         * @param string $mime Optional, MIME type
         * @return string
         */
        private function get_mime_ext($mime=NULL) {
            if($mime===NULL)
                $mime = $this->mime;
            else
                $mime = $this->strip_mime($mime); 
            
             foreach($this->no_magic as $ext => $info) 
                if($info['mime_type'] == $mime ) 
                    return $this->no_magic[$ext]['fn_extension'][0]; 
        }

        
        
        /**
         * Tell BOOL TRUE if this is textual data, includes xml,html,css, javascript
         */
        public function isTextualData() {
            if($this->get_mime_class()=='text') return TRUE;
            
            if(is_int($slp = strpos($this->mime,'/'))) {
                if( is_int(strpos($this->mime,'xml',$slp)) or is_int(strpos($this->mime,'ecma',$slp)) ) 
                return TRUE;
            }
            return FALSE;
        }
    
        
        /**
         * Returns formatted output
         * @return string
         */
        private function format_output($flags=FILEINFO_MIME, $mime=NULL) {
            
            //1040
            if(($flags & FILEINFO_MIME)==FILEINFO_MIME) return $this->mime . (($this->charset_f != '') ? '; ' . $this->charset_f : '');
            
            if(($flags & FILEINFO_MIME_TYPE)==FILEINFO_MIME_TYPE) return $this->mime;//16
            if(($flags & FILEINFO_MIME_ENCODING)==FILEINFO_MIME_ENCODING) return $this->charset_f;//1024
            
        }
        
        
        /**
         * Parses file / finfo_file() output
         * 
         * file v4 gives only combined result   rfc2045 
         * @param string $fmime
         */
        private function parse_fileinfo_mime(&$fmime, &$content) {
            $this->mime = $this->strip_mime($fmime);
            // $this->charset_f used by getXMLencoding(); Helps to ignore finfo detected encoding.             
            $this->charset_f = trim( (is_int(strpos($fmime,';')) ? substr($fmime,strpos($fmime,'=')+1,strlen($fmime)) : ''));
            if($this->charset_f == 'unknown' or $this->charset_f=='binary') $this->charset_f = '';
            if($this->charset_f==='' && $this->isTextualData()) {
                $this->getXMLencoding($content); 
                return TRUE;           
            }
            
        }

        /**
         * Strip and teim all found before ";param=value"
         * @param string $mime
         * @return string
         */
        private function strip_mime($mime) {
            return trim( 
                substr( $mime, 0, (is_int($p=strpos($mime,';')) ? $p : strlen($mime)) ) 
                ,"\t\n\r\x00 -" );
        }
        
        /**
         * Tries to find BOM or first XML/HTML/CSS/JS charset/encoding found in markup.
         * 
         * @param unknown_type $content
         * @return string
         */
        private function getXMLencoding(&$content) {
            
            if(function_exists('mb_detect_encoding')) {
                $this->charset_f = mb_detect_encoding($content, 'UTF-8,UTF-7,ASCII');
                if(is_string($this->charset_f)) {
                    return TRUE; 
                }
            }
            
            $enc_bom = $charset_p = $charset_e = $c = $r = NULL;            
            $enc_sets = array(
            'UTF-32LE'=>   "\x00\x00\xFE\xFF",
            'UTF-32BE'=>   "\xFF\xFE\x00\x00",
            'UTF-EBCDIC'=> "\xDD\x73\x66\x73",
            'EBCDIC'   =>  "\x4C\x6F\xA7\x94\x93",
            'UTF-16BE'=>   "\xFE\xFF", 
            'UTF-16LE'=>   "\xFF\xFE",
            'UTF-8'=>      "\xEF\xBB\xBF", 
            'UTF-7'=>      "\x2B\x2F\x76",
            'UTF-1'=>      "\xF7\x64\x4C",
            'SCSU'=>       "\x0E\xFE\xFF", 
            'BOCU-1'=>     "\xFB\xEE\x28",
            //'BE'=>"\xFE", //'LE'=>"\xFF"
            );
            
            foreach($enc_sets as $enc_name=>$enc_set) {
                if( substr($content,0,strlen($enc_set)) == $enc_set ) $enc_bom = $enc_name;
            }
            
            $content_len = strlen($content);
            
            //$content_len_d4 = ($content_len % 4)==0; //32
            //$content_len_d2 = ($content_len % 2)==0; //16
            
            if($enc_bom===NULL) {
                //$low_chars="\x0\x1\x2\x3\x8\x\x9\x\xA\xB\xC\xD\x1A\x1D\x1E\x1F"; or is_int(strpos($low_chars, $content[$i]))
                for($i=$ascii=0;$i<$content_len;$i++) {
                    $char = ord($content[$i]); 
                    if($char > 127) $ascii++; 
                }
                if($ascii==0) {
                    $enc_bom = 'ASCII';
                }
                
            }
            
            $offset = strpos($content,'@charset');
            if(is_int($offset))
                $c = $this->getfrange($content, '"', '"', $offset, FALSE);
            
                
                
            if(!is_string($c)) {
                $offset = strpos($content,'<?');
                if(is_int($offset))
                    $r = $this->getfrange($content, '<'.'?xml', '?'.'>', $offset, FALSE);
               
                if(is_string($r)) {
                    $offset = strpos($r,'encoding');
                    if(is_int($offset)) {
                        $c = $this->getfrange($r, '"', '"', $offset, FALSE);
                    }
                } 
            }
            
            
            if(!is_string($c)) {
                
                $cl = stripos($content,'</head>'); if(!is_int($cl)) $cl = stripos($content,'<body');
                $head_c1280 = 
                preg_replace('/[\t\n\r ]+/', ' ', 
                    str_replace("'" ,'"' ,
                        preg_replace("/(<\/?)(w+)([^>]*>)/e", "'\\1'.mb_strtolower('\\2').'\\3'", 
                            substr($content, 0 ,$cl )
                        )
                    )
                );
                            
                $meta='meta';
                $offset = 0;
                while($meta) {
                    $meta = $this->getfrange($head_c1280, '<meta', '>', $offset, FALSE);
                    
                    if(is_string($meta)) {
                        $charset_p = strpos($meta,'charset');
                        if(is_int($charset_p)) {
                            $charset_e = strpos($meta,'=',$charset_p);
                        } 
                        if(is_int($charset_e)) {
                            
                            //for old / long
                            $c = $this->getfrange($meta, '=', '"', $charset_e, FALSE);
                                                    
                            // for new / sort
                            if(is_string($c) && strlen($c)==0) {
                                $charset_e = 0;
                                $c = $this->getfrange($meta, '"', '"', $charset_e, FALSE);
                            }
                            
                            if(is_string($c)) {
                                $c = ltrim($c,'"= ');
                                $meta = FALSE;                                                                            
                            }
                       } 
                    } 
                }
            }

            if(is_string($c)) {
                   $this->charset_f = strtoupper($c); 
            } 
            elseif(is_string($enc_bom)) {
                   $this->charset_f = $enc_bom;
            }
            else { 
                  $this->charset_f = '';
            }
            
        }       
        /**
         * Get range without recusrion
         *
         * @param string $s source
         * @param string $b begin
         * @param string $e end
         * @param int $o offset default is 0
         * @param bool $ibe include begin and end
         * @return string , bool FALSE otherwise
         * @author http://ukj.pri.ee
         */
        private function getfrange(&$s, $b, $e, &$o, $ibe=FALSE) {
        
            // $o =(int) $o;
            if($o > strlen($s)) return FALSE;
            if($o < 0) $o = 0;
            
            $pb = strpos($s, $b, $o );
            if ( $pb === FALSE ) return FALSE;
            $lb = strlen( $b );
            
            $pe = strpos($s, $e , $pb+$lb );
            if( $pe === FALSE ) return FALSE;
            $le = strlen( $e );
            $ls = strlen( $s );
        
            if($o > $ls) return FALSE;
            if($ibe)
                $sr = $b . substr($s, $pb+$lb, $pe-$pb-$lb ) . $e;
            else
                $sr = substr($s, $pb+$lb, $pe-$pb-$lb );
            $o=$pe+$le;
            //echo " $b -> ";
            
            return $sr;
        } //getfrange()

        /**
         *  Searches the array for a given value and returns corresponding key.
         *
         * $a = array('a'=>'order:0', 'n'=>'order:1',2=>'order:2','s'=>'order:3'); 
         * echo "\n" . arrayKeyOfValue('order:1',$a) .", " . arrayKeyOfValue('order:3',$a) . " \n"; 
         * // n, s
         *
         * @param mixed $value
         * @param array $array
         * @return int order of key/value, FALSE if key not found
         * @author http://ukj.pri.ee
         */
        private function arrayKeyOfValue($value, &$array) {
            if(!is_array($array))return FALSE;
            if(in_array($value, $array)) {
                foreach($array as $key => $v) if($value == $v) return $key; 
            }
            return NULL;
        } //arrayKeyOfValue()   

        
        /**
         * Returns first 30 splits as array; 
         * @param string $e explode by
         * @param string $s & string to explode
         * @param int $slices Number of slices
         * @return array
         */
       private function explode_head($e, &$s, $slices=30) {
       $l = strlen($s);
       $h = array();
       $o=0;
       $enc = mb_detect_encoding($s, 'UTF-8,UTF-7,ASCII'); if(!$enc) $enc = 'UTF-8';
       $el=mb_strlen($e, $enc);
       $ls=mb_strlen($s, $enc);
       for($i=0;$i<$slices;$i++) {
            if($o < $ls) {
                $p = mb_strpos($s, $e, $o, $enc);
            }
           if(is_int($p) ) {
               $h[] = mb_strcut($s, $o, $p-$o, $enc);
               $o = $p+$el;
           } else {
                $h[] = mb_strcut($s, $o, $ls, $enc);
               return $h;
           }
       }
       return $h;   
    }
    
    
    
}



    
    if(!function_exists('mime_content_type')) {
        /**
         * Returns the content type in MIME format
         * @param $filename
         */
        function mime_content_type($filename) {   
            $fi = new myfinfo(FILEINFO_MIME|FILEINFO_PRESERVE_ATIME);
            if (!$fi) return FALSE;
            return $finfo->file($filename);
        }
    }



?>
<?phpnamespace Util;class Captcha {    private $width;    private $height;    private $codeNum;    private $code;    private $im;    function __construct($width=80, $height=20, $codeNum=4) {        $this->width = $width;        $this->height = $height;        $this->codeNum = $codeNum;    }    function showImg() {        $this->createImg();        $this->setDisturb();        $this->setCaptcha();        $this->outputImg();    }    function getCaptcha() {        return $this->code;    }    private function createImg() {        $this->im = imagecreatetruecolor($this->width, $this->height);        $bgColor = imagecolorallocate($this->im, 0, 0, 0);        imagefill($this->im, 0, 0, $bgColor);    }    private function setDisturb() {        $area = ($this->width * $this->height) / 20;        $disturbNum = ($area > 20) ? 20 : $area;        for ($i = 0; $i < $disturbNum; $i++) {            $color = imagecolorallocate($this->im, rand(0, 255), rand(0, 255), rand(0, 255));            imagesetpixel($this->im, rand(1, $this->width - 2), rand(1, $this->height - 2), $color);        }        for ($i = 0; $i <= 2; $i++) {            $color = imagecolorallocate($this->im, rand(128, 255), rand(125, 255), rand(100, 255));            imagearc($this->im, rand(0, $this->width), rand(0, $this->height), 0, 30, 50, 30, $color);        }    }    private function createCode() {        $salt = \GlobalEnv::get('app')->config('captcha-salt');        for ($i = 0; $i < $this->codeNum; $i++) {            $this->code .= $salt{rand(0, strlen($salt) - 1)};        }    }    private function setCaptcha() {        $this->createCode();        for ($i = 0; $i < $this->codeNum; $i++) {            $color = imagecolorallocate($this->im, 250, 250, 250);            $size = rand(floor($this->height / 5), floor($this->height / 3));            $x = floor($this->width / $this->codeNum) * $i + 5;            $y = rand(0, $this->height - 20);            imagechar($this->im, $size, $x, $y, $this->code{$i}, $color);        }    }    private function outputImg() {        if (imagetypes() & IMG_JPG) {            header('Content-type:image/jpeg');            imagejpeg($this->im);        } elseif (imagetypes() & IMG_GIF) {            header('Content-type: image/gif');            imagegif($this->im);        } elseif (imagetypes() & IMG_PNG) {            header('Content-type: image/png');            imagepng($this->im);        } else {            die("Don't support image type!");        }    }}
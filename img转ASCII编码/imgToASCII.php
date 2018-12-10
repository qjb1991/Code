<?php
class ImgToASCII
{
    protected $img_text;
    protected $img_data;
//    public    $ascii_str = '@#&$8RGA90Q27y=ro|i!;:"`-_. ';//12个个字符
//    protected $str = "~!@#$%^&*()_+1234567890-=qwertyuiop[]\\asdfghjkl;'zxcvbnm,.QWERTYUIOP{}|ASDFGHJKL:\"ZXCVBNM<>?";
    public    $ascii_str = 'MWNQqpHAgpOU@#%ER804526tJD)"j][sawf\(x*7ll11__++rr!!::;;~~^\'\',,,```...---   ';//字符
    protected $config    = array(
        'img_path' => '',
        'step_x'   => 1,
        'step_y'   => 1,
    );

    public function __construct($param=array())
    {
        if(is_array($param)){
            $this->config=array_intersect_key($param,$this->config);
        }else{
            $this->config['img_path']=$param;
        }
//        var_dump($this->config);
    }


    /**
     * 将文件转为gd句柄
     * @param null $img_path
     * @return resource
     * @throws Exception
     */
    public function getImg($img_path = null)
    {
        $img_path == null && $img_path = $this->config['img_path'];
        $arr = getimagesize($img_path);
//        var_dump($arr);
        if ($arr[2] == 1) {
            $this->img_data = imagecreatefromgif($img_path);
        } else if ($arr[2] == 2) {
            $this->img_data = imagecreatefromjpeg($img_path);
        } else if ($arr[2] == 3) {
            $this->img_data = imagecreatefrompng($img_path);
        } else {
            throw new \Exception("对不起，暂不支持该格式！");
        }
        return $this;
    }

    public function handleImg($img_data = null)
    {
        $img_data==null  && $img_data=$this->img_data;
        $x_y_array = $this->getImgXY($img_data);
        $output    = "";
        for ($j = 0; $j < $x_y_array['y']; $j += $this->config['step_x']) {
            for ($i = 0; $i < $x_y_array['x']; $i +=  $this->config['step_x']) {
                $colors = imagecolorsforindex($img_data, imagecolorat($img_data, $i, $j));    //获取像素块的代表点RGB信息
                $greyness = $this->countGray($colors['red'], $colors["green"], $colors["blue"]) / 255;    //灰度值计算公式：Gray=R*0.3+G*0.59+B*0.11

                $offset   = (int)floor($greyness * (strlen($this->ascii_str) - 1));    //根据灰度值选择合适的字符
                if ($offset == (strlen($this->ascii_str) - 1))
                    $output .= " ";    //替换空格为 ；方便网页输出
                else
                    $output .= $this->ascii_str[$offset];
            }
            $output .= "\r\n";
        }
        imagedestroy($img_data);
        return $output;
//        echo $output;
    }

    /**
     * 测试字符颜色深度
     */
    public function test()
    {

        $str = $this->ascii_str;
//        $str = "~!@#$%^&*()_+1234567890-=qwertyuiop[]\\asdfghjkl;'zxcvbnm,.QWERTYUIOP{}|ASDFGHJKL:\"ZXCVBNM<>?";
        $str = str_split($str, 20);
//        var_dump($str);return;
        $output = '';

        for ($i = 0; $i <= 50; $i++) {
            $str0=str_split($str[0]);
            foreach ($str0 as $value) {
                $output .= str_repeat($value, 50);
            }
            $output .= "\r\n";
        }


        $str1=str_split($str[1]);
        for ($i = 0; $i <= 50; $i++) {
            foreach ($str1 as $value) {
                $output .= str_repeat($value, 50);
            }
            $output .= "\r\n";
        }


        $str2=str_split($str[2]);
        for ($i = 0; $i <= 50; $i++) {
            foreach ($str2 as $value) {
                $output .= str_repeat($value, 50);
            }
            $output .= "\r\n";
        }


        $str3=str_split($str[3]);
        for ($i = 0; $i <= 50; $i++) {
            foreach ($str3 as $value) {
                $output .= str_repeat($value, 50);
            }
            $output .= "\r\n";
        }


        $str4=str_split($str[4]);
        for ($i = 0; $i <= 50; $i++) {
            foreach ($str4 as $value) {
                $output .= str_repeat($value, 50);
            }
            $output .= "\r\n";
        }
        file_put_contents('1.text',$output);
//        echo $output;
    }


    /**
     * 获取图片长宽值
     * @param null $img_data
     * @return array
     */
    public function getImgXY($img_data = null)
    {
        $img_data == null && $img_data = $this->img_data;
        $arr = array(
            'x' => imagesx($img_data),
            'y' => imagesy($img_data),
        );
        return $arr;
    }


    /**
     * 计算灰度值
     * @param $r
     * @param $g
     * @param $b
     * @return float|int
     */
    public function countGray($r, $g, $b)
    {
        $gray = ($r * 299 + $g * 587 + $b * 114 + 500) / 1000;
        return $gray;
    }
}


$img = new ImgToASCII(array(
    'img_path'=>'test.jpg',
    'step_x'=>5,//步进值,越小精度越高,1
    'step_y'=>5
));
$output=$img->getImg()->handleImg();
file_put_contents('1.txt',$output);
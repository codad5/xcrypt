<?php
class xcrypt{
    public function __construct() {
    }

    public static function getChars() {
        $chars = strtolower("ABCDEFGHIJKLMNOPQRStUVWXYZ");
        $chars .= strtoupper("ABCDEFGHIJKLMNOPQRStUVWXYZ");
        $chars .= strtolower("!@~`#$%^&*()_+=-{}[]|\\:;<>,.?/123456789");
        $chars = str_split($chars);
        $chars[] = ' ';
        return $chars;
    }

    public static function en($text, $key){
        $char_lenght = count(self::getChars());
        if($key > $char_lenght) throw new Exception("Max Encryption Length is $char_lenght");
        $text = str_split($text);
        $exp = "";
        foreach ($text as $va) {
            $pos = array_search($va, self::getChars(), true);
            if($pos === false) throw new Exception("Cant Encrypt $va", 1);
            $left = $pos - $key >= 0 ? self::getChars()[$pos - $key] : self::getChars()[$char_lenght + ($pos - $key)];
            $right = $pos + $key < $char_lenght ? self::getChars()[$pos + $key] : self::getChars()[($pos + $key) - $char_lenght];
            $exp .= $left.$right;
        }
        return $exp;
    }
    public static function dc($encrypted_text, $key){
        $chars = self::getChars();
        $char_length = count($chars);
        if($key > $char_length) throw new Exception("Max Encryption Length is $char_length");
        $encrypted_text = str_split($encrypted_text, 2);
        // return $key;
        // var_dump($nn);
        $exp = "";
        foreach ($encrypted_text as $va) {
            # code...
            $splitted = str_split($va);
            $left = array_search($splitted[0], $chars, true);
            $right = array_search($splitted[1], $chars, true);
            $left = $left + $key < $char_length ? $left + $key : ($left + $key) - $char_length;
            $right = $right - $key >= 0 ? $right - $key : $char_length + $right - $key;
            // var_dump($left , $right, $splitted, $va);
            // echo "========\n";
            if($left  !== $right) throw new Exception("Invalid Encryption");
            $exp .= $chars[$left];
        }
        // var_dump($key);
        return $exp;
    }
    public static function verify($encrypted_text, $text, $key){
        // var_dump($text , self::unlock($encrypted_text, $key));
        return $text === self::dc($encrypted_text, $key);
    }
    public static function lock($text, $key){
        return self::en(self::en($text, $key), $key);
    }
    public static function unlock($encrypted_text, $key){
        return self::dc(self::dc($encrypted_text, $key), $key);
    }
}


function get_key($prompt = 'Your encryption key: '){
    $key = (int) readline($prompt);
    $max = count(xcrypt::getChars());
    if($key < 1 || $key > $max){
        $key = get_key("Key can be lower  than 1 and greater than $max: ");
    }
    return (int) $key;
}


try{
    $key = get_key();
    $text = readline("Write text you want to encrypt: ");
    readline_add_history($text);
    $enc = xcrypt::en($text, $key);
    echo "Encrypted: $enc \n";
    $key = get_key("Enter Your decryption key: ");
    $dec = xcrypt::dc($enc, $key);
    $verify = xcrypt::verify($enc, $text, $key) ? 'true' : 'false';
    echo "Key Verified: $verify \n";
    sleep(2);
    echo "Decrypted: $dec \n";
}
catch(Exception $e){
    echo $e->getMessage();

}

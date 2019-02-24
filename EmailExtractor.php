<?php

class EmailExtractor
{
    /**
     * @var string
     */
    private $filename;

    /**
     * EmailExtractor constructor.
     * @param string $filename
     */
    public function __construct($filename)
    {
        $this->filename = $filename;
    }

    /**
     * @param string $filename
     * @return bool|string
     */
    private function fileIntoString($filename)
    {
        if (file_exists($filename))
            return file_get_contents($filename, true);
        else
            return false;
    }

    /**
     * @param array $emails
     */
    private function writeToFile($emails)
    {
        if (!empty($emails)) {
            foreach ($emails as $email) {
                $file = fopen('emails.txt', 'a+');
                fwrite($file, $email . "\r\n");
                fclose($file);
            }
        }
    }

    /**
     * Extracts emails from text
     */
    public function extract()
    {
        $db_str = $this->fileIntoString($this->filename);

        if ($db_str) {
            $pattern = '~[^\s]+@[^\s]+~';
            //$pattern = "#<(.*?)>#";
            preg_match_all($pattern, trim($db_str), $matches);

            $emails = array();
            foreach ($matches[0] as $match) {
                $email = trim($match, "<>");
                $email = filter_var($email, FILTER_VALIDATE_EMAIL);
                if ($email)
                    $emails[] = $email;
            }
            $emails = array_values(array_unique($emails));

            $this->writeToFile($emails);

            echo "Найдено " . count($emails) . " адресов эл. почты.";
            //echo "<pre>"; print_r($emails); echo "</pre>";
        } else
            echo "Файл не найден.";
    }
}
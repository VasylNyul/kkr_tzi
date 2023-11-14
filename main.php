<?php
require_once('vendor/autoload.php');

use phpseclib\Crypt\Twofish;

class EncryptionSystem
{
    private $cipher;
    private $encryptionKey;
    private $encryptedData;

    public function __construct()
    {
        $this->cipher = new Twofish();
        $this->encryptionKey = '';
        $this->encryptedData = '';
    }

    /**
     * @throws Exception
     */
    public function setKey($key): void
    {
        if (empty($key) || mb_strlen($key, 'UTF-8') % 16 !== 0) {
            throw new Exception("Недійсний ключ. Ключ повинен мати довжину, кратну 16.");
        }

        $this->cipher->setKey($key);
        $this->encryptionKey = $key;
    }

    /**
     * @throws Exception
     */
    public function encrypt($data): string
    {
        if ($this->cipher->getKeyLength() == 0) {
            throw new Exception("Ключ не встановлено. Встановіть ключ перед шифруванням.");
        }

        $this->encryptedData = base64_encode($this->cipher->encrypt($data));
        return $this->encryptedData;
    }

    /**
     * @throws Exception
     */
    public function decrypt()
    {
        if ($this->cipher->getKeyLength() == 0) {
            throw new Exception("Ключ не встановлено. Встановіть ключ перед розшифруванням.");
        }

        return $this->cipher->decrypt(base64_decode($this->encryptedData));
    }

    public function checkEncryptionKey($key): bool
    {
        return ($this->encryptionKey === $key);
    }
}

// Створення екземпляра системи шифрування
$encryptionSystem = new EncryptionSystem();

try {
    $stop = false;
    while ($stop === false) {
        echo "Оберіть операцію:\n";
        echo "1. Зашифрувати\n";
        echo "2. Розшифрувати\n";
        echo "3. Вихід\n\n";
        echo "Ваш вибір: ";

        $choice = trim(fgets(STDIN));
        $data = '';

        switch ($choice) {
            case '1':
                echo "Введіть дані для шифрування: ";
                $data = trim(fgets(STDIN));

                echo "Введіть ключ: ";
                $key = trim(fgets(STDIN));
                $encryptionSystem->setKey($key);

                $encryptedData = $encryptionSystem->encrypt($data);
                echo "Зашифровані дані: ".$encryptedData."\n";
                break;

            case '2':
                echo "Введіть ключ: ";
                $key = trim(fgets(STDIN));
                while ($encryptionSystem->checkEncryptionKey($key) != true) {
                    echo "Ключ не вірний, спробуйте ще раз.\n";
                    echo "Введіть ключ: ";
                    $key = trim(fgets(STDIN));
                }
                $encryptionSystem->setKey($key);

                $decryptedData = $encryptionSystem->decrypt();
                echo "Розшифровані дані: ".$decryptedData."\n";
                break;

            case '3':
                $stop = true;
                break;


            default:
                echo "Невірний вибір. Оберіть операцію 1 або 2.\n";
        }
    }
} catch (Exception $e) {
    echo "Помилка: ".$e->getMessage()."\n";
}

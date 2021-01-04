# linphe

###route案例
use Yjtec\Linphe\Lib\Router;

if (php_sapi_name() == "cli") {// 只允许在cli下面运行 
    if (!class_exists('\\Redis', false)) {
        die('必须开启Redis扩展' . PHP_EOL);
    }
//消费者
    Router::cli("/worker(\/.*)*/u", "app\\worker\\worker", 'start');
} else {
//上传
    Router::post("/upload/u", "app\\index", 'upload');
//查询
    Router::get("/status\/[0-9]*/u", "app\\index", 'status');
    Router::get("/notify/u", "app\\index", 'notify');
//管理员
    Router::get("/admin\/jobs(\/[0-9]*)(\/[0-9]*)/u", "app\\admin\\mkpano\\jobs", 'jlist');
}
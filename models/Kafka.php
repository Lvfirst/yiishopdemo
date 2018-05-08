<?php 
namespace app\models;

class Kafka
{
    public $broker_list='192.168.137.129:9092'; //kafka服务器

    public $topic='topic'; // 定义topic主题

    public $partition=0; // kafka的物理分组

    // public $logFile='@app/runtime/logs/kafka/info.log'; //设定日志存储的地方


    protected $producer=null; //生产者
    protected $consumer=null; // 消费者



    /**
     * [__construct description]
     *
     * @DateTime 2018-05-07
     */
    public function __construct()
    {
        if(empty($this->broker_list))
        {
            throw new \yii\base\InvalidConfigException("broker not exists");
        }

        // 判断能否启用kafka生产者
        $rk=new \RdKafka\Producer();
        if(empty($rk))
        {
            throw new \yii\base\InvalidConfigException('producer no exists');
        }
        $rk->setLogLevel(LOG_DEBUG);
        //判断能不能监听
        if(!$rk->addBrokers($this->broker_list))
        {
            throw new \yii\base\InvalidConfigException('producer no exists');
        }
        // 对象赋予成员属性
        $this->producer=$rk;
    }

    public function send($message=[])
    {
        // 新建一个topic 主题
        $topic=$this->producer->newTopic($this->topic);

        /**
         * 1.第一个参数是分区。RD_KAFKA_PARTITION_UA表示未分配，并让librdkafka选择分区
         * 2. 第二个参数是消息标志，并且应该始终为0
         * 3. 消息有效载荷可以是任何东西
         */
        // 生产消息
        return $topic->produce(RD_KAFKA_PARTITION_UA,$this->partition,json_encode($message));
    }


    public function consumer($object,$callback)
    {
        $conf=new \RdKafka\Conf(); // 创建配置
        $conf->set('group.id',0); //设定group的id  默认是0
        $conf->set('metadata.broker.list',$this->broker_list);  //绑定broker_list

        $topicConf=new \RdKafka\TopicConf();
        $topicConf->set('auto.offset.reset','smallest'); //从开头消费生产的消息  --from-beginning

        // 设定默认的 TopicConf
        $conf->setDefaultTopicConf($topicConf);   //这里是设定topicConf ,不是conf

        $consumer=new \RdKafka\KafkaConsumer($conf);

        // 订阅消息
        $consumer->subscribe([$this->topic]);  //可订阅多个,传递数组

        echo "waiting for messages.....\n";

        while(true){

            $message=$consumer->consume(120*1000);
            switch($message->err)
            {
                //没有任何错误则返回消息
            case RD_KAFKA_RESP_ERR_NO_ERROR:
                echo 'message payload...';
                // \Yii::info($message->payload);
                $object->$callback($message->payload);
                break;
            }

            sleep(2);
        }
    }

}

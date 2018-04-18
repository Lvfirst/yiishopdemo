<?php 

	sudo chkconfig --add elasticsearch
### You can start elasticsearch service by executing
 sudo service elasticsearch start
  Verifying  : elasticsearch-2.4.1-1.noarch     
 ?>

 curl -XPUT 192.168.137.236:9200/imooc_shopp/products/1 '{"productid":1,"title":"这是一个标题","descr":"这是一个商品描述"}'

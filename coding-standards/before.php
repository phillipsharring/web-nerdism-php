<?php
declare(strict_types=1);
$items=[['name'=>'Mug','cents'=>1200,'qty'=>2],['name'=>'Shirt','cents'=>2000,'qty'=>1]];
$total=0;
foreach($items as $item)
{
  $line=$item['cents']*$item['qty'];
  $total+=$line;
  if($item['qty']>1){ echo $item['qty'].' x '.$item['name'].': '.number_format($line/100,2)."\n"; }
  else { echo $item['name'].': '.number_format($line/100,2)."\n"; }
}
echo 'Total: '.number_format($total/100,2)."\n";

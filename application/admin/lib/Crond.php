<?php
namespace app\admin\lib;

use think\Config;
use think\console\Command;
use think\console\Input;
use think\console\Output;

/**
 * Created by PhpStorm.
 * User: jichongrong
 * Date: 2018/5/30
 * Time: 下午3:26
 */

class Crond extends Command
{
	protected function configure()
	{
		$this->setName('Cron')->setDescription('计划任务');
	}

	protected function execute(Input $input, Output $output)
	{
		$this->doCron();
		$output->writeln("已经执行计划任务");
	}

	public function doCron()
	{
		// 记录开始运行的时间
		$GLOBALS['_beginTime'] = microtime(TRUE);

		/* 永不超时 */
		ini_set('max_execution_time', 0);
		$time   = time();
		$exe_method = [];
		$crond_list = config('param.crond_list');   //获取第四步的文件配置，根据自己版本调整一下
		// dump($crond_list);
		$sys_crond_timer = config('param.sys_crond_timer');
		foreach ( $sys_crond_timer as $format )
		{
			if($format=='i'){
				//没5分钟一次
				$key = (int)date($format, ceil($time))%5==0?5:0;
			}else{
				$key = date($format, ceil($time));
			}

			if(isset($crond_list[$key])){
				if ( is_array(@$crond_list[$key]) )
				{
					$exe_method = array_merge($exe_method, $crond_list[$key]);
				}
			}
            if($format=='i'){
                $key = (int)date($format, ceil($time))%10==0?10:0;
                if(isset($crond_list[$key])){
                    if ( is_array(@$crond_list[$key]) )
                    {
                        $exe_method = array_merge($exe_method, $crond_list[$key]);
                    }
                }
            }
		}



		if (!empty($exe_method))
		{

			foreach ($exe_method as $method)
			{
				if(!is_callable($method))
				{
					//方法不存在的话就跳过不执行
					continue;
				}

				// echo "执行crond --- {$method}()\n";
				$runtime_start = microtime(true);

				call_user_func($method);
				$runtime = microtime(true) - $runtime_start;

//				echo "{$method}(), 执行时间: {$runtime}\n\n";
			}

			$time_total = microtime(true) - $GLOBALS['_beginTime'];
			// echo "total:{$time_total}\n";
		}
	}
}
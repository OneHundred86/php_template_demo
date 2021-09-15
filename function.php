<?php
/**
 * 模版的工具函数
 */ 



/**
 * 编译view模版(这里只处理了{{}})
 * @return string $cache_file
 */
function compile(string $view, array $config) : string
{
	$view_template_file = $config['template_path'] . $view . ".html";
	$content = file_get_contents($view_template_file);

	$compile_content = preg_replace_callback('#{{(.*)}}#', 
		function(array $matches){
			return sprintf('<?php echo e(%s); ?>', trim($matches[1]));
		},
		$content);

	$cache_file = $config['cache_path'] . md5($view) . ".php";

	file_put_contents($cache_file, $compile_content);

	return $cache_file;
}

/**
 * 渲染模版
 * @return string
 */ 
function vender(string $view, array $data = [], ?array $config = null) : string
{
	# 编译
	if(!$config){
		$config = [
			'template_path' => "./template/",
			'cache_path' => "./cache/",
		];
	}

	$file = compile('test', $config);

	# 渲染，重点
	ob_start();
	extract($data, EXTR_SKIP);	// 导入变量

	try{
		include $file;		// 打印模版
	}catch(\Exception $e){
		ob_end_clean();
		throw $e;
	}

	$content = ob_get_clean();

	return $content;
}

/**
 * 打印模版变量
 * @return string
 */
function e($value, $doubleEncode = true)
{
	if ($value instanceof Htmlable) {
		return $value->toHtml();
	}

	return htmlspecialchars($value, ENT_QUOTES, 'UTF-8', $doubleEncode);
}
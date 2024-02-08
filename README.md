# Hyperf Dump Server

## 介绍

Hyperf内监听dump函数打印数据持续输出，且不干扰当前HTTP/API上下文的响应。

**目前Hyperf框架要求最低3.1**

## 安装

安装依赖

```bash
composer require --dev zyimm/hyperf-dump-server
```

发布组件配置

host 配置监听地址

```bash
php bin/hyperf.php vendor:publish zyimm/hyperf-dump-server
```

## 使用

开启监听

```bash
php bin/hyperf.php dump-server
```

使用  `--format` 将监听数据指向某个文件

```bash
# 以html文本格式保存到当前dump.html文本中
php bin/hyperf.php dump-server --format=html > dump.html
```

⚠️symfony/var-dumper的`dd` 函数不要使用，因为它会退出当前进程！


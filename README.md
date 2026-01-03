# 大学生匿名心理测试与树洞系统（ThinkPHP5 示范版）

本示例项目提供了基于 **ThinkPHP5 架构与 MVC 组织** 的匿名心理测评与树洞分享后台原型，涵盖验证码、加盐登录、图片上传、统计图表数据接口、分级权限的后台管理以及备份/恢复能力，便于二次开发与集成。当前版本改为 **MySQL 数据库存储**，包含自动建表迁移。

## 功能概览
- **匿名心理测试**：提交问卷答案计算得分并留存历史。
- **树洞发布**：匿名发布树洞内容与标签，便于统计热点。
- **验证码**：简单算术验证码接口，防止机器滥用。
- **加盐密码**：后台用户注册/认证使用随机盐 + SHA256。
- **图片上传**：后台管理员上传图片（含类型/大小校验）。
- **统计功能（≥5 项）**：
  1. 测试总次数 `total_tests`
  2. 平均得分 `average_score`
  3. 树洞数量 `treehole_posts`
  4. 按日期汇总的测试/树洞趋势 `daily_tests`、`daily_posts`
  5. 热门标签 Top5 `top_tags`
  6. 分数分布直方图 `score_distribution`
- **后台权限分级**：`super` 可管理备份与用户，`editor` 仅可查看统计。
- **备份/恢复**：压缩存储数据并支持解压恢复。
- **图表数据接口**：`/stats/overview` 返回可直接喂给前端图表组件的数据。

## 目录结构
```
public/index.php                # 前端控制器，路由入口
application/
  common/                       # Bootstrap & 辅助函数
  config/config.php             # 基础配置（角色、上传限制、盐长度）
  controller/                   # MVC 控制器
  model/                        # 数据模型（MySQL 存储）
  service/                      # 业务服务（验证码、上传、统计、备份等）
storage/                        # 数据、上传、备份目录
```

## 数据库准备
1. 在 MySQL 创建数据库（示例名 `mental_treehole`）并配置账号：
   ```sql
   CREATE DATABASE mental_treehole DEFAULT CHARACTER SET utf8mb4;
   ```
2. 编辑 `application/config/config.php` 中的 `db` 配置，写入主机、端口、库名、用户名、密码。
3. 首次运行时会自动迁移创建所需表：`users`, `test_results`, `treehole_posts`, `captcha_tokens`。

### 在 phpstudy 上运行（示例步骤）
1. 使用 phpstudy 内置 MySQL，新建数据库 `mental_treehole`，并在“管理”里为 `root` 账户设置密码（例如 `root` 或你自定义的强密码）。
2. 在 phpstudy 面板中添加站点，站点根目录指向项目的 `public/` 目录（或保持默认根目录并将 `public` 设为运行目录）。
3. 打开 `application/config/config.php`，根据 phpstudy 的 MySQL 端口（默认 3306 或面板显示的端口）与密码，填写：
   ```php
   'db' => [
       'host' => '127.0.0.1',
       'port' => 3306,      // 若 phpstudy 指定其他端口，请同步修改
       'database' => 'mental_treehole',
       'username' => 'root',
       'password' => '你的密码',
       'charset' => 'utf8mb4',
   ],
   ```
4. 保存后，启动 phpstudy 的 Web 服务（Apache/Nginx 均可）。首次访问任意接口会自动建表，无需手动导入 SQL。
5. 参考下方“快速开始”创建管理员并调用接口。

## 快速开始
1. **创建管理员**：在 CLI 中运行一次 PHP 片段注册管理员（示例）。
   ```bash
   php -r "require 'application/common/bootstrap.php'; (new app\\service\\AuthService())->registerAdmin('admin','Admin@123','super'); echo \"admin created\\n\";"
   ```
2. **获取验证码**：`GET /index.php?r=auth/captcha`
3. **管理员登录**：`POST /index.php?r=auth/login`，需携带用户名、密码与验证码。
4. **提交测试**：`POST /index.php?r=test/submit`，`answers[]` 为分值数组。
5. **树洞发布/列表**：
   - `POST /index.php?r=treehole/post`，`content`、`tags[]`
   - `GET /index.php?r=treehole/list`
6. **统计数据**：`GET /index.php?r=stats/overview`
7. **备份与恢复**（超级管理员）：
   - 备份：`POST /index.php?r=backup/create`
   - 恢复：`POST /index.php?r=backup/restore`，参数 `filename`

> 本示例采用文件存储与最小依赖，便于演示与教学。生产环境建议替换为数据库存储、完善的会话管理与表单/权限校验。

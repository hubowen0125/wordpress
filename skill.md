你现在是一名资深 WordPress 主题开发工程师。

请直接检查当前工作目录中的 WordPress 项目，并为这个全新的 WordPress 站点开发、部署一个独立的自定义主题。

不要只提供教程或示例代码，需要实际创建文件、写入代码、检查语法，并在环境允许时完成主题启用和首页配置。

## 一、执行前检查

开始修改之前，请先完成以下检查：

1. 确认当前目录是不是 WordPress 根目录。
2. 确认是否存在以下目录和文件：

   * wp-admin
   * wp-includes
   * wp-content
   * wp-config.php
3. 检查当前 WordPress 版本和 PHP 版本。
4. 检查是否安装并可以使用 WP-CLI。
5. 检查当前是否已有主题或自定义代码。
6. 检查项目根目录或上级目录是否存在 AGENTS.md，并遵守其中的开发规则。
7. 不要修改 WordPress 核心文件。
8. 不要覆盖已有主题。
9. 不要删除已有文章、页面、分类、插件或数据库内容。
10. 如果当前目录不是 WordPress 根目录，请停止写文件，并明确告诉我正确的目录应该是什么。

检查完成后，先简要输出：

* 当前 WordPress 环境
* PHP 版本
* 是否可使用 WP-CLI
* 准备创建的主题路径
* 准备修改或创建的文件
* 是否发现可能冲突的已有内容

然后继续执行开发，不需要等待我确认。

---

## 二、主题基本信息

创建一个新的经典 WordPress 主题。

主题名称：

Market Pulse

主题目录：

wp-content/themes/market-pulse

主题 Text Domain：

market-pulse

主题定位：

加密货币、贵金属和金融市场资讯网站。

默认视觉风格：

* 深色金融科技风格
* 页面背景使用深灰或接近黑色
* 卡片使用略浅的深色背景
* 文字清晰、对比度足够
* 上涨使用绿色
* 下跌使用红色
* 强调色可以使用蓝色
* 圆角适中
* 不使用夸张渐变
* 不依赖 Elementor
* 不依赖付费插件
* 不依赖 Tailwind CSS
* 不依赖 Bootstrap
* 不使用 jQuery，除非 WordPress 本身功能确实需要
* 使用原生 PHP、HTML、CSS 和 JavaScript

---

## 三、主题文件结构

至少创建以下文件：

wp-content/themes/market-pulse/
├── style.css
├── functions.php
├── header.php
├── footer.php
├── front-page.php
├── home.php
├── archive.php
├── category.php
├── single.php
├── page.php
├── index.php
├── search.php
├── 404.php
├── screenshot.png
├── assets/
│   ├── css/
│   │   └── main.css
│   ├── js/
│   │   ├── market-widgets.js
│   │   └── navigation.js
│   └── images/
└── template-parts/
├── content-card.php
├── content-none.php
└── market-price-card.php

如果某些文件没有必要重复代码，请使用 WordPress 模板函数和 template part 复用，不要复制大量相同代码。

---

## 四、首页布局

首页必须使用 front-page.php。

首页整体最大宽度约为 1200px，居中显示，桌面端左右保留间距。

首页结构必须按照下面的顺序实现：

┌────────────────────────────────────────┐
│ BTC / ETH / GOLD / SILVER 行情滚动条  │
├────────────────────┬───────────────────┤
│ BTC 实时价格卡片   │ GOLD 实时价格卡片│
├────────────────────┼───────────────────┤
│ ETH 实时价格卡片   │ SILVER 实时价格卡片│
├────────────────────────────────────────┤
│                                        │
│          TradingView 大型行情图        │
│                                        │
├────────────────────────────────────────┤
│ 最新资讯，共展示 6 篇                  │
└────────────────────────────────────────┘

### 4.1 顶部行情滚动条

使用 TradingView 官方 Ticker Tape Widget。

展示以下品种：

* Bitcoin / BTCUSD
* Ethereum / ETHUSD
* Gold / XAUUSD
* Silver / XAGUSD

优先尝试以下 TradingView Symbol：

* BITSTAMP:BTCUSD
* BITSTAMP:ETHUSD
* OANDA:XAUUSD
* OANDA:XAGUSD

如果某个 Symbol 在 TradingView 中无法正常加载，请选择可用的同类官方市场 Symbol，并把 Symbol 集中配置在一个 PHP 数组或 JavaScript 配置对象中，不要分散写死在多个模板文件。

要求：

* 宽度占满首页内容区域
* 自动滚动
* 深色主题
* 隐藏无关内容
* 移动端不能横向撑破页面
* 加载失败时保留合理高度，不能导致页面布局跳动
* TradingView 脚本只加载一次

### 4.2 四个实时价格卡片

创建四个行情卡片：

1. Bitcoin
2. Gold
3. Ethereum
4. Silver

桌面端两列布局：

第一行：

* 左侧 Bitcoin
* 右侧 Gold

第二行：

* 左侧 Ethereum
* 右侧 Silver

移动端改为单列。

每张卡片至少显示：

* 品种名称
* 交易代码
* 当前价格
* 当日涨跌金额
* 当日涨跌百分比
* 一个小型价格趋势图
* 最后更新时间或 TradingView 提供的状态
* “查看图表”按钮

可以使用 TradingView 官方 Ticker、Symbol Overview、Mini Chart 或同类官方小组件实现。

不要通过 PHP 在服务器端抓取 TradingView 页面。

不要解析或爬取 TradingView HTML。

不要伪造实时价格。

如果第三方行情组件暂时没有返回数据，显示：

“行情数据加载中”

不要显示虚假的静态价格。

每个卡片需要有唯一容器 ID，避免多个 TradingView 组件发生冲突。

### 4.3 大型行情图

使用 TradingView 官方 Advanced Chart Widget。

默认展示：

BITSTAMP:BTCUSD

在图表上方增加四个切换按钮：

* BTC
* ETH
* GOLD
* SILVER

点击按钮时切换大型图表的 Symbol。

对应关系集中配置：

* BTC：BITSTAMP:BTCUSD
* ETH：BITSTAMP:ETHUSD
* GOLD：OANDA:XAUUSD
* SILVER：OANDA:XAGUSD

要求：

* 默认周期为日线或 60 分钟周期
* 深色主题
* 宽度 100%
* 桌面端高度约 560px
* 平板端高度约 480px
* 手机端高度约 420px
* 支持全屏按钮
* 支持常用技术指标
* 支持蜡烛图
* 不允许图表溢出父容器
* 切换品种时显示加载状态
* 切换后高亮当前按钮
* 不要重复无限添加 TradingView script 标签
* 切换图表时正确销毁或清空旧容器
* 避免出现多个图表重叠

如果 TradingView 当前组件不支持直接动态修改 Symbol，可以清空图表容器并重新初始化组件。

### 4.4 最新资讯

大型行情图下方展示最新发布的 6 篇 WordPress 文章。

使用 WP_Query 获取：

* post_type：post
* post_status：publish
* posts_per_page：6
* ignore_sticky_posts：true

每篇文章卡片显示：

* 特色图片
* 分类名称
* 文章标题
* 文章摘要
* 发布时间
* 作者
* “阅读全文”链接

要求：

* 桌面端三列
* 平板端两列
* 手机端一列
* 标题最多显示两到三行
* 摘要长度统一
* 没有特色图片时显示主题内置占位图
* 使用 esc_url、esc_html、esc_attr 等 WordPress 转义函数
* 循环结束后执行 wp_reset_postdata()
* 首页最新资讯下方增加“查看更多资讯”按钮，链接到文章列表页

---

## 五、文章列表页

文章列表页使用 home.php。

archive.php 和 category.php 复用相同的文章卡片组件。

列表页需要包含：

* 页面标题
* 当前分类说明
* 文章卡片列表
* 分页
* 无文章状态
* 面包屑导航

文章卡片展示：

* 特色图片
* 分类
* 标题
* 摘要
* 发布时间
* 作者
* 详情链接

布局要求：

* 桌面端三列
* 平板端两列
* 手机端一列
* 使用 WordPress 主查询
* 不要为了列表页重新创建不必要的 WP_Query
* 分页使用 the_posts_pagination()
* 分类页标题使用 single_cat_title()
* 分类说明使用 category_description()

---

## 六、文章详情页

文章详情页使用 single.php。

页面结构：

1. 面包屑
2. 文章分类
3. H1 标题
4. 发布时间
5. 更新时间
6. 作者
7. 特色图片
8. 正文
9. 标签
10. 上一篇和下一篇
11. 相关文章
12. 返回资讯列表按钮

详情页要求：

* 正文宽度适合阅读
* H1 只能有一个
* 正确输出 the_content()
* 图片自适应
* 表格在手机端可以横向滚动
* 长英文、URL 和数字不能撑破页面
* 支持 Gutenberg 区块
* 支持代码块
* 支持引用块
* 支持有序和无序列表
* 支持文章分页 wp_link_pages()
* 相关文章优先读取相同分类
* 相关文章排除当前文章
* 相关文章最多显示 3 篇
* 不显示未发布文章
* 使用 wp_reset_postdata()

---

## 七、Header 和 Footer

### Header

需要包含：

* 网站 Logo
* 网站名称
* 首页链接
* 资讯链接
* 分类菜单
* 搜索按钮
* 移动端菜单按钮

要求：

* 使用 wp_nav_menu()
* 注册 primary 菜单位置
* 支持后台设置自定义 Logo
* 使用 the_custom_logo()
* Header 可以吸顶
* 移动端菜单可以展开和关闭
* 菜单按钮包含 aria-expanded 和 aria-controls
* ESC 键可以关闭菜单
* 不要使用内联 onclick

### Footer

需要包含：

* 网站名称
* 简短说明
* Footer 菜单
* 当前年份
* WordPress 隐私政策链接
* 金融风险提示

风险提示文案：

“本站行情及资讯仅供参考，不构成任何投资建议。加密货币及贵金属市场存在较高风险，请根据自身情况谨慎决策。”

年份必须使用 PHP 动态生成，不能写死。

header.php 中必须调用 wp_head()。

footer.php 中必须调用 wp_footer()。

body 开始后调用 wp_body_open()。

---

## 八、functions.php

在 functions.php 中完成：

1. add_theme_support('title-tag')
2. add_theme_support('post-thumbnails')
3. add_theme_support('custom-logo')
4. add_theme_support('html5')
5. add_theme_support('responsive-embeds')
6. add_theme_support('align-wide')
7. 注册 primary 和 footer 菜单
8. 使用 wp_enqueue_style() 加载样式
9. 使用 wp_enqueue_script() 加载 JavaScript
10. 使用 filemtime() 生成本地静态资源版本号
11. 禁止在模板中直接硬编码本地 CSS、JS 路径
12. 注册合理的图片尺寸
13. 提供默认特色图片处理函数
14. 提供面包屑函数
15. 提供统一的文章摘要函数
16. 对公共函数添加 market_pulse_ 前缀，防止命名冲突

不要关闭 WordPress 自动更新。

不要删除 WordPress 默认 REST API。

不要禁用 Gutenberg。

不要加入来源不明的代码。

---

## 九、首页和资讯页面配置

如果 WP-CLI 可用，请以幂等方式完成以下配置：

1. 检查是否已经存在标题为“首页”的页面。
2. 如果不存在，创建一个已发布页面：

   * 标题：首页
   * slug：home
3. 检查是否已经存在标题为“资讯”的页面。
4. 如果不存在，创建一个已发布页面：

   * 标题：资讯
   * slug：news
5. 将 WordPress 阅读设置改为：

   * show_on_front = page
   * page_on_front = 首页页面 ID
   * page_for_posts = 资讯页面 ID
6. 设置固定链接为文章名称结构：
   /%postname%/
7. 刷新 rewrite rules。
8. 激活 market-pulse 主题。

操作必须幂等：

* 页面已经存在时不能重复创建
* 不得删除原页面
* 不得删除文章
* 不得覆盖已有菜单
* 不得重复执行产生多份相同数据

如果 WP-CLI 不可用，请不要直接操作数据库。

这种情况下，请输出需要我在 WordPress 中文后台执行的准确步骤：

设置 → 阅读 → 您的主页显示 → 一个静态页面

并说明：

* 主页选择“首页”
* 文章页选择“资讯”

---

## 十、响应式要求

断点建议：

* 大于等于 1024px：桌面布局
* 768px 至 1023px：平板布局
* 小于 768px：手机布局

必须重点检查以下尺寸：

* 1440px
* 1024px
* 768px
* 390px
* 375px

要求：

* 页面不能出现非预期横向滚动条
* 图片不能超出容器
* TradingView iframe 不能超出容器
* 长标题不能破坏卡片布局
* 英文 URL 可以换行
* 行情卡片在手机端为单列
* 导航菜单在手机端可以正常使用
* 按钮点击区域至少适合触屏操作

CSS 中至少包含：

* box-sizing: border-box
* img { max-width: 100%; height: auto; }
* overflow-wrap: anywhere
* TradingView 容器宽度限制
* iframe 最大宽度限制

---

## 十一、SEO 和语义化

需要实现：

* 每页只有一个主 H1
* 使用 header、nav、main、section、article、footer
* 文章卡片标题层级正确
* Logo 和图片有 alt
* 链接文字有明确含义
* 保留 WordPress title-tag
* 不要手动输出重复的 title 标签
* 分类页可以被搜索引擎正常访问
* 分页链接正常
* 文章详情页 canonical 交给 WordPress 或 SEO 插件处理
* 不要在主题中硬编码站点域名
* 所有站内 URL 使用 WordPress 函数生成

---

## 十二、性能要求

1. TradingView 脚本按需加载。
2. 不要重复加载相同第三方脚本。
3. 首页以外的页面不要加载 market-widgets.js。
4. 本地 JavaScript 使用 defer 或放在 footer。
5. 文章图片启用 WordPress 默认懒加载。
6. 不引入大型前端框架。
7. 不下载或打包来源不明的第三方脚本。
8. 不把实时行情数据写入 WordPress 数据库。
9. 不在每次访问首页时执行远程 PHP 请求。
10. 给 TradingView 容器设置最小高度，减少布局偏移。

---

## 十三、安全要求

* 所有动态文本正确转义
* 所有 URL 使用 esc_url()
* HTML 属性使用 esc_attr()
* 普通文本使用 esc_html()
* 允许的文章正文通过 the_content() 输出
* 不直接拼接未经处理的 $_GET、$_POST、$_REQUEST
* 不直接写 SQL
* 不修改 wp-config.php 中的数据库配置
* 不输出服务器敏感信息
* 不在代码中保存账号、密码、Token 或 API Key
* 不使用 eval()
* 不使用 base64 解码执行代码
* 不允许从远程地址下载 PHP 文件并执行

---

## 十四、错误处理

TradingView 加载失败时：

* 页面其他内容仍然可以正常显示
* 行情区域显示“行情数据暂时无法加载”
* 不产生持续重复请求
* 不在控制台无限输出错误
* 不因为一个行情组件失败而影响其他组件
* 给第三方组件添加合理的加载状态

没有文章时：

* 显示“暂时没有资讯”
* 页面结构保持完整

没有特色图片时：

* 使用主题占位图片
* 不输出损坏的 img 标签

---

## 十五、代码质量

要求：

* 遵守 WordPress Coding Standards
* PHP 代码兼容当前服务器 PHP 版本
* JavaScript 不污染全局作用域
* 使用严格、清晰的函数命名
* 不在多个文件重复相同逻辑
* 不把大量 CSS 写入 PHP 模板
* 不把大量 HTML 写入 functions.php
* 对复杂逻辑添加简短注释
* 文件编码使用 UTF-8
* 所有 PHP 文件避免不必要的结束标签
* 不保留调试用 var_dump、print_r、console.log 或 alert

---

## 十六、测试与验收

开发完成后执行以下检查：

### PHP 检查

对所有主题 PHP 文件执行：

php -l 文件名

确保没有 PHP 语法错误。

### WordPress 检查

确认：

* 主题可以正常激活
* 首页没有白屏
* 后台可以正常访问
* 首页设置正确
* 资讯列表正常
* 分类列表正常
* 文章详情正常
* 404 页面正常
* 搜索页面正常
* 菜单功能正常
* 自定义 Logo 正常
* 特色图片正常
* 分页正常

### 前端检查

确认：

* BTC、ETH、GOLD、SILVER 滚动行情存在
* 四张价格卡片都能独立加载
* 大型图表默认展示 BTC
* 四个图表切换按钮可以使用
* 最新资讯准确显示 6 篇
* 手机端没有横向滚动
* TradingView 组件不会覆盖其他内容
* 控制台没有由主题代码导致的 JavaScript 错误

### 最终输出

完成后，请输出：

1. 环境检查结果
2. 创建的主题目录
3. 新增文件列表
4. 修改文件列表
5. 首页和资讯页配置结果
6. TradingView 使用的 Symbol 配置
7. 执行过的命令
8. PHP 语法测试结果
9. 未能自动完成的步骤
10. WordPress 中文后台需要手动执行的步骤
11. 访问首页、资讯列表和示例文章的 URL
12. 关键文件及关键代码所在行号
13. 后续建议，但不要擅自继续安装插件

请现在开始检查并实际执行，不要只生成方案。

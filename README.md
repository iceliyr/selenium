## 项目介绍

该项目主要通过selenium+python为对电子商务网站进行自动化测试



## 内容介绍

**网站环境安装** 目录为安装测试网站及selenium工具

**测试用例设计**  分析及编写测试用例

**Selenium使用** 结合分析网站对selenium的介绍及使用



## 综合实验

#### 实验要求

设计一套页面操作流程，进行一系列操作，至少包含如下内容：浏览器前进后退，窗口大小操作，获取文本框里的数据或获取静态文本数据并进行处理，获取按钮或复选框的状态，选中某一按钮或复选框，模拟鼠标的单击、双击、右击等操作，模拟键盘的复制、粘贴、输入等操作，对弹出消息框进行确定、取消、截屏操作，切换浏览器窗口和Frame，页面元素截屏操作，验证码获取操作等。

#### 代码

```
# 作者：
# 华南师范大学21软工8班 李艺儒
# 完成时间：
# 2024/06/13
# 测试环境：
# python3.12,
# selenium4.21.0,
# ddddocr1.5.2

from time import sleep
from selenium import webdriver
from selenium.webdriver import ActionChains, Keys
from selenium.webdriver.common.by import By
from selenium.webdriver.support.select import Select
from PIL import ImageGrab
import ddddocr

driver = webdriver.Firefox()
driver.get("http://localhost/upload/index.php")

#  窗口大小操作
driver.set_window_size(200, 200)
driver.set_window_position(10, 10)
sleep(3)
# 最小
driver.minimize_window()
sleep(3)
# 最大
driver.maximize_window()
sleep(3)

#  浏览器前进后退操作
# 登录
driver.find_element(By.CSS_SELECTOR,"#ECS_MEMBERZONE > a:nth-child(2) > img:nth-child(1)").click()
sleep(3)
# 后退
driver.back()
url = driver.current_url
sleep(3)
# 前进
driver.forward()
sleep(3)
driver.get(url)

#  对弹出消息框进行确定、取消
driver.find_element(By.NAME,'imageField').click()
sleep(3)
# 确定
driver.switch_to.alert.accept()
driver.find_element(By.ID,'keyword').send_keys('806')
driver.find_element(By.NAME,'imageField').click()
sleep(3)
driver.find_element(By.LINK_TEXT,'P806').click()
sleep(3)
driver.find_element(By.XPATH,'/html/body/div[7]/div[2]/div[1]/div[2]/form/ul/li[9]/a[1]/img').click()
sleep(6)
driver.find_element(By.LINK_TEXT,'删除').click()
sleep(3)
# 取消
driver.switch_to.alert.dismiss()
driver.find_element(By.LINK_TEXT,'首页').click()
sleep(3)

# 获取文本框的数据
driver.find_element(By.LINK_TEXT,"P806").click()
num = driver.find_element(By.ID,"number")
tex1=num.get_attribute("value")
print(f"手机数量是：{tex1}")
sleep(3)

#  获取按钮状态、选中按钮
butt = driver.find_element(By.ID,"spec_value_168")
if not butt.is_selected():
    butt.click()
    sleep(3)
print("总价为", driver.find_element(By.ID,"ECS_GOODS_AMOUNT").text)
sleep(3)

#  模拟鼠标的单击、双击、右击等操作，
# 单击左键留言板
ActionChains(driver).move_to_element(driver.find_element(By.CSS_SELECTOR,"#mainNav > a:nth-child(10)")).click().perform()
sleep(3)
# 单击右键
ActionChains(driver).move_to_element(driver.find_element(By.ID,"keyword")).context_click().perform()
# 单击左键
ActionChains(driver).move_to_element(driver.find_element(By.ID,"keyword")).click().perform()
sleep(3)
# 双击左键
ActionChains(driver).move_to_element(driver.find_element(By.NAME,"user_email")).double_click().perform()
sleep(3)
ema = driver.find_element(By.NAME,"user_email")
ema.send_keys("lee@ecshop.com")
sleep(3)

#  模拟键盘的复制、粘贴、输入等操作，
ema.send_keys(Keys.HOME)
ema.send_keys(Keys.SHIFT, Keys.ARROW_RIGHT)
ema.send_keys(Keys.SHIFT, Keys.ARROW_RIGHT)
ema.send_keys(Keys.SHIFT, Keys.ARROW_RIGHT)
# 复制
ema.send_keys(Keys.CONTROL, 'c')
# 输入
ActionChains(driver).send_keys(Keys.TAB).send_keys(Keys.ARROW_RIGHT).send_keys(Keys.ARROW_RIGHT).send_keys(Keys.TAB).send_keys("询问").perform()
sleep(3)
com = driver.find_element(By.NAME,"msg_content")
com.send_keys("我是")
# 粘贴
com.send_keys(Keys.CONTROL, 'v')
sleep(3)
com.send_keys("，有真人吗")
sleep(3)
driver.get("http://localhost/upload/admin/index.php")
ActionChains(driver).move_to_element(driver.find_element(By.NAME,"username")).click().send_keys("admin").send_keys(Keys.TAB).send_keys("admin123").perform()
sleep(3)

# 验证码获取操作
img = driver.find_element(By.XPATH,'/html/body/form/table/tbody/tr/td[2]/table/tbody/tr[4]/td/img')
img.screenshot("captcha.png")
img_bytes = open("captcha.png", "rb").read()
ocr = ddddocr.DdddOcr()
check_code = ocr.classification(img_bytes)
driver.find_element(By.NAME,'captcha').send_keys(check_code)
driver.find_element(By.CLASS_NAME,"button").click()
sleep(3)

# 下拉列表操作
driver.switch_to.frame('header-frame')
driver.find_element(By.LINK_TEXT,'个人设置').click()
sleep(5)
driver.switch_to.default_content()
driver.switch_to.frame('main-frame')
s1=driver.find_element(By.ID,'all_menu_list')
s2=Select(s1)
for i in range(0,6):
    s2.select_by_index(i)
sleep(5)
s2.deselect_by_visible_text('商品管理')
s2.deselect_by_index(1)
s2.deselect_by_value('comment_manage.php?act=list')
sleep(5)
s2.deselect_all()
s2.select_by_visible_text('    商品类型')
sleep(3)
a=driver.find_element(By.ID,'btnAdd')
if a.is_enabled():
    a.click()
    sleep(5)
s3=driver.find_element(By.ID,'menus_navlist')
s4=Select(s3)
l1=len(s4.options)
for i in range(l1-2,l1):
    s4.select_by_index(i)
    sleep(3)
t1=s4.all_selected_options
for i in range(0,2):
   print(t1[i].text)
print(s4.first_selected_option.text)

#  页面元素截屏操作以及切换frame和窗口
driver.switch_to.default_content()
driver.switch_to.frame('header-frame')
driver.find_element(By.LINK_TEXT,'起始页').click()
sleep(3)
driver.save_screenshot(r".\lee\1.png")
driver.switch_to.default_content()
driver.switch_to.frame("menu-frame")
driver.find_element(By.CSS_SELECTOR,"body").screenshot(r".\lee\2.png")
driver.find_element(By.CSS_SELECTOR,"li.explode:nth-child(1) > ul:nth-child(1) > li:nth-child(1) > a:nth-child(1)").click()
sleep(3)
# 切换frame
driver.switch_to.default_content()
driver.switch_to.frame("main-frame")
driver.find_element(By.XPATH,'//span[text()="夏新N7"]/../../td[11]/a/img').click()
sleep(3)
# 切换窗口
lst=driver.window_handles
driver.switch_to.window(lst[-1])
driver.find_element(By.CSS_SELECTOR,"#ECS_MEMBERZONE > a:nth-child(2) > img:nth-child(1)").click()
sleep(3)
driver.find_element(By.CSS_SELECTOR,".us_Submit").click()
sleep(2)
# 对消息框截屏
ImageGrab.grab().save(r".\lee\3.png")
driver.switch_to.alert.accept()
driver.find_element(By.NAME,"username").send_keys("vip")
driver.find_element(By.NAME,'password').send_keys("vip")
driver.find_element(By.CSS_SELECTOR,".us_Submit").click()
sleep(3)

```


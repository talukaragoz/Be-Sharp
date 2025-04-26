from bs4 import BeautifulSoup
import shutil
import string

# Ask for the name of the article
article_name = input("What is the name of the article?\n")
article_name = article_name.translate(str.maketrans('', '', string.punctuation))

article_link = "articles/" + article_name.lower().replace(" ", "") + ".html"
home_page_path = "../index.html"

# Create copy of example html page for the article
shutil.copyfile("articles/example.html", article_link)

# Editing the home page
# -------------------------------
soup = BeautifulSoup(open(home_page_path[3:], "rb"))

link_section = soup.find("div", {"id" : "links-section"})

# Create element for link section
hyperlink = soup.new_tag("a", href= article_link)
hyperlink.append(article_name)

new_link = soup.new_tag("p")
new_link["class"] = "article-title"
new_link.append(hyperlink)

link_section.insert(0, new_link)

# Save the edited home page
with open(home_page_path[3:], "w", encoding="utf-8") as f:
    f.write(soup.prettify())
# -------------------------------

# Editing the article
# -------------------------------
article = input("Please provide the article itself:\n")
# Separate article into paragraphs
paragraphs = article.split("  ")

soup = BeautifulSoup(open(article_link, "rb"))

# Change page title for browswer
page_title = soup.find("title")
page_title.append(article_name + " - Be Sharp")

# Adding article title
title = soup.find("div", {"class" : "container"})

# Create inner element with back link
name = soup.new_tag("a", href=home_page_path)
name.append(article_name)

# Create outer element to add to the div
back_link = soup.new_tag("h1")
back_link.append(name)

# Insert complete title element to div
title.insert(0, back_link)

# Adding actual article
article_div = soup.find("div", {"class" : "article-content"})

for paragraph in paragraphs:
    new_paragraph = soup.new_tag("p")
    new_paragraph.append(paragraph)
    article_div.append(new_paragraph)

# Saving the edited HTML
with open(article_link, "w", encoding="utf-8") as f:
    f.write(soup.prettify())
# -------------------------------

print("Successfully integrated new article")
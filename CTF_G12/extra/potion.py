import random

def generateDIV(i, j, l):
    # potion rates
    potions = random.randint(1, 1000)
    p = ""
    if potions <= 1:
        p = "vase"
    elif potions <= 11:
        p = "red_potion"
    elif potions <= 161:
        p = "lift_potion"
    elif potions <= 400:
        p = "love_potion"
    elif potions <= 700:
        p = "blue_potion"
    elif potions <= 1000:
        p = "pink_potion"
    else:
        p = "vase"


    bc = random.randint(1, 100);
    if bc <= 1:
        bc = random.randint(1, 3);
        if bc == 1:
            bc = "This is the one!"
        elif bc == 2:
            bc = "Bob"
        else:
            bc = "This one looks special"
    elif bc <= 30:
        bc = random.randint(1, 3);
        if bc == 1:
            bc = "The boss will like this one."
        elif bc == 2:
            bc = "I'M A GREAT ASSET TO THE COMPANY"
        else:
            bc = "HO HOO! This will make me employee of the month."
    else:
        bc = random.randint(1, 4);
        if bc == 1:
            bc = "Ohh, shiny!"
        elif bc == 2:
            bc = "Looks tasty..."
        elif bc == 3:
            bc = "Wow, so pretty!"
        else:
            bc = "I wonder what it tastes like..."


    # param number
    t = l[5*i + j - 1]
    rlink = "http://academy.repo/redirect.html?t="
    return f"""<div class="potion-item"><a href="{rlink}{t}"><figure><img src="./{p}.png" height="175" title="{bc}"></figure></a></div>"""

# number of potions (n % 5 == 0)
n = 6700

# Shuffle list range
nums = [x+1 for x in range(n)]
random.shuffle(nums)

# Finished potion.html
result = """
<!DOCTYPE html>
<html>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POTION HALL</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Chakra+Petch:wght@400;600;700&family=Space+Mono:wght@400;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="potions.css">
</html>

<body>
    <div class="vignette"></div>
    <div class="filler"></div>
    <div class="potion-container">
        <p class="academy-text">Great job arriving at the academy!<br> There are a lot of potions around here, but only some are of value.<br>In order to go back home from a good day of work, you must find the valuable loots scattered around the room!</p>
    </div>
    <div class="potion-container">
"""
for i in range(n//5):
    result += (" "*8) + """<div class="potion-row">"""
    result += "\n"
    for j in range(5):
        result += " "*12
        result += generateDIV(i, j, nums)
        result += "\n"
    result += (" "*8) + "</div>\n"
result += (" "*4) + """</div>
</body>""";

# this is so you can python3 > potions.html
print(result)
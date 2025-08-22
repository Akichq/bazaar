#!/bin/bash

# 画像ディレクトリを作成
docker-compose exec php mkdir -p storage/app/public/items

# 必要な画像ファイルをコピー
images=(
    "Armani_Mens_Clock.jpg"
    "HDD_Hard_Disk.jpg"
    "iLoveIMG_d.jpg"
    "Leather_Shoes_Product_Photo.jpg"
    "Living_Room_Laptop.jpg"
    "Music_Mic_4632231.jpg"
    "Purse_fashion_pocket.jpg"
    "Tumbler_souvenir.jpg"
    "Waitress_with_Coffee_Grinder.jpg"
    "Makeup_Set.jpg"
)

for img in "${images[@]}"; do
    docker-compose exec php cp tests/Feature/dummy.jpg storage/app/public/items/$img
    echo "Created: $img"
done

echo "All images created successfully!"

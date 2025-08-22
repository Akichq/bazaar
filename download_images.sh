#!/bin/bash

# 画像ディレクトリを作成
docker-compose exec php mkdir -p storage/app/public/items

# 画像URLとファイル名のマッピング
declare -A images=(
    ["Armani_Mens_Clock.jpg"]="https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Armani+Mens+Clock.jpg"
    ["HDD_Hard_Disk.jpg"]="https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/HDD+Hard+Disk.jpg"
    ["iLoveIMG_d.jpg"]="https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/iLoveIMG+d.jpg"
    ["Leather_Shoes_Product_Photo.jpg"]="https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Leather+Shoes+Product+Photo.jpg"
    ["Living_Room_Laptop.jpg"]="https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Living+Room+Laptop.jpg"
    ["Music_Mic_4632231.jpg"]="https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Music+Mic+4632231.jpg"
    ["Purse_fashion_pocket.jpg"]="https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Purse+fashion+pocket.jpg"
    ["Tumbler_souvenir.jpg"]="https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Tumbler+souvenir.jpg"
    ["Waitress_with_Coffee_Grinder.jpg"]="https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Waitress+with+Coffee+Grinder.jpg"
    ["Makeup_Set.jpg"]="https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/%E5%A4%96%E5%87%BA%E3%83%A1%E3%82%A4%E3%82%AF%E3%82%A2%E3%83%83%E3%83%95%E3%82%9A%E3%82%BB%E3%83%83%E3%83%88.jpg"
)

# 各画像をダウンロード
for filename in "${!images[@]}"; do
    url="${images[$filename]}"
    echo "Downloading: $filename"
    docker-compose exec php curl -o "storage/app/public/items/$filename" "$url"
done

echo "All images downloaded successfully!"

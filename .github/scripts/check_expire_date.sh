#!/bin/bash

# 本日の日付を Y-m-d 形式で取得
current_date=$(date +%Y-%m-%d)

# pull request で新規追加・変更されたファイルのリスト
changed_files=$(git diff-tree --no-commit-id --name-only -r HEAD)

for file in $changed_files; do
    # ファイルが削除による変更であればスキップ
    if [ ! -f "$file" ]; then
        continue
    fi

    # ファイル内の [Expire] タグを検索
    grep -Fn "[Expire]" "$file" | while read -r line ; do
        line_number=$(echo "$line" | cut -d: -f1)
        line_content=$(echo "$line" | cut -d: -f2-)

        # 日付を抽出（[Expire] Y-m-d の形式を想定）
        # \K はそこ以前の [Expire] を省略し、Y-m-d の部分のみを取得する
        expire_date=$(echo $line_content | grep -oP '\[Expire\] \K\d{4}-\d{2}-\d{2}')

        # 日付が抽出できなかった場合、または日付が過去の場合にエラー
        if [[ -z $expire_date ]] || [[ $expire_date < $current_date ]]; then
            // ファイル名と行番号を取得する
            echo "Invalid or expired date found: $expire_date in $file:$line_number"
            exit 1
        fi
    done
done

# 正常終了
exit 0
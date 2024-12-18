import pandas as pd
import folium

df = pd.read_csv("898.csv")

store = df[["緯度","経度","店舗名(日本語)"]].values

m = folium.Map(location=[35.942957, 136.190], zoom_start=16)
for data in store:
    folium.Marker([data[0], data[1]], tooltip=data[2]).add_to(m)
m.save('store.html')

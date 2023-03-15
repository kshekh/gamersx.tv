<template>
  <!-- remove "text-white" later -->
  <div class="text-white py-4 md:py-7">
    <template v-if="settings.rows.length">
      <div v-for="(row, index) in settings.rows" :key="row.id">
        <component
          :is="row.componentName"
          :settings="row"
          :rowPosition="index"
        ></component>
      </div>
    </template>
    <div v-else>
      <template v-if="cachedSkeletonRows.length">
        <div>
          <div v-for="(row, index) in cachedSkeletonRows" :key="index">
            <component
              :is="row+'Skeleton'"
              :rowPosition="index"
            ></component>
          </div>
        </div>
      </template>
      <template v-else>
        <div>
          <div v-for="(row, index) in defaultSkeletonRows" :key="index">
            <component
              :is="row"
              :rowPosition="index"
            ></component>
          </div>
        </div>
      </template>
<!--      <component v-else :is="defaultSkeleton"></component>-->
    </div>
  </div>
</template>
<script>
import axios from "axios";
import LazyLoadComponent from './LazyLoad';
import FullWidthDescriptiveSkeleton from "./skeletons/FullWidthDescriptiveSkeleton";
import ClassicSmSkeleton from "./skeletons/ClassicSmSkeleton.vue";
import ClassicMdSkeleton from "./skeletons/ClassicMdSkeleton.vue";
import ClassicLgSkeleton from "./skeletons/ClassicLgSkeleton.vue";
import ClassicVerticalSkeleton from "./skeletons/ClassicVerticalSkeleton.vue";
import NumberedRowSkeleton from "./skeletons/NumberedRowSkeleton.vue";
import ParallaxSkeleton from "./skeletons/ParallaxSkeleton.vue";
import FullWidthImagerySkeleton from "./skeletons/FullWidthImagerySkeleton.vue";


export default {
  components: {
    FullWidthDescriptiveSkeleton: FullWidthDescriptiveSkeleton,
    ClassicSmSkeleton: ClassicSmSkeleton,
    ClassicMdSkeleton: ClassicMdSkeleton,
    ClassicLgSkeleton: ClassicLgSkeleton,
    NumberedRowSkeleton: NumberedRowSkeleton,
    ParallaxSkeleton: ParallaxSkeleton,
    ClassicVerticalSkeleton: ClassicVerticalSkeleton,
    FullWidthImagerySkeleton: FullWidthImagerySkeleton,

    FullWidthDescriptive: LazyLoadComponent({
      componentFactory: () => import('./front/FullWidthDescriptive.vue'),
      loading: FullWidthDescriptiveSkeleton,
    }),

    Parallax: LazyLoadComponent({
      componentFactory: () => import('./front/Parallax.vue'),
      loading: ParallaxSkeleton,
    }),

    NumberedRow: LazyLoadComponent({
      componentFactory: () => import('./front/NumberedRow.vue'),
      loading: NumberedRowSkeleton,
    }),

    ClassicSm: LazyLoadComponent({
      componentFactory: () => import('./front/ClassicSm.vue'),
      loading: ClassicSmSkeleton,
      loadingData: 200
    }),

    FullWidthImagery: LazyLoadComponent({
      componentFactory: () => import('./front/FullWidthImagery.vue'),
      loading: FullWidthImagerySkeleton,
    }),

    ClassicVertical: LazyLoadComponent({
      componentFactory: () => import('./front/ClassicVertical.vue'),
      loading: ClassicVerticalSkeleton,
    }),

    ClassicMd: LazyLoadComponent({
      componentFactory: () => import('./front/ClassicMd.vue'),
      loading: ClassicMdSkeleton,
    }),
    ClassicLg: LazyLoadComponent({
      componentFactory: () => import('./front/ClassicLg.vue'),
      loading: ClassicLgSkeleton,
    }),
  },
  data: function () {
    return {
      settings: {
        // rows: [],
        rows: [
    {
        "title": "Main Display",
        "sortIndex": -100,
        "componentName": "FullWidthDescriptive",
        "onGamersXtv": false,
        "channels": [
            {
                "info": {
                    "id": "516575",
                    "name": "VALORANT",
                    "box_art_url": "https://static-cdn.jtvnw.net/ttv-boxart/516575-{width}x{height}.jpg",
                    "igdb_id": "126459"
                },
                "broadcast": {
                    "id": "40149049303",
                    "user_id": "50988750",
                    "user_login": "stylishnoob4",
                    "user_name": "stylishnoob4",
                    "game_id": "516575",
                    "game_name": "VALORANT",
                    "type": "live",
                    "title": "フェイドってだれですかVALORANT教えてください",
                    "viewer_count": 25420,
                    "started_at": "2023-03-14T06:37:49Z",
                    "language": "ja",
                    "thumbnail_url": "https://static-cdn.jtvnw.net/previews-ttv/live_user_stylishnoob4-{width}x{height}.jpg",
                    "tag_ids": [],
                    "tags": null,
                    "is_mature": false
                },
                "liveViewerCount": 25420,
                "viewedCount": 0,
                "showOnline": true,
                "onlineDisplay": {
                    "title": "stylishnoob4 playing VALORANT for 25420 viewers",
                    "showArt": false,
                    "showEmbed": true,
                    "showOverlay": true
                },
                "offlineDisplay": {
                    "title": "VALORANT",
                    "showArt": false,
                    "showEmbed": false,
                    "showOverlay": false
                },
                "itemType": "game",
                "rowName": "Main Display",
                "sortIndex": 0,
                "image": null,
                "overlay": null,
                "customArt": null,
                "link": "https://www.twitch.tv/directory/game/VALORANT",
                "componentName": "EmbedContainer",
                "embedName": "TwitchEmbed",
                "embedData": {
                    "channel": "stylishnoob4",
                    "elementId": "embed-64106d7e16c0f"
                },
                "description": null,
                "isGlowStyling": "enabled_if_live"
            },
            {
                "info": {
                    "id": "124425627",
                    "login": "lpl",
                    "display_name": "LPL",
                    "type": "",
                    "broadcaster_type": "partner",
                    "description": "Official Twitch Channel of the League of Legends Pro League (LPL). For more information, schedules and stats head to Lolesports.com. #CrazyIsOurGame",
                    "profile_image_url": "https://static-cdn.jtvnw.net/jtv_user_pictures/8111888b-17e2-43b6-ac5f-10bb3451b130-profile_image-300x300.png",
                    "offline_image_url": "https://static-cdn.jtvnw.net/jtv_user_pictures/8f757f88-176d-4e83-930b-bceff3a39a62-channel_offline_image-1920x1080.png",
                    "view_count": 109558663,
                    "created_at": "2016-05-16T18:37:03Z"
                },
                "broadcast": {
                    "id": "40149190215",
                    "user_id": "124425627",
                    "user_login": "lpl",
                    "user_name": "LPL",
                    "game_id": "21779",
                    "game_name": "League of Legends",
                    "type": "live",
                    "title": "RA VS. UP | NIP VS. IG - Week 9 Day 2 | LPL Spring Split (2023)",
                    "viewer_count": 14009,
                    "started_at": "2023-03-14T07:58:26Z",
                    "language": "en",
                    "thumbnail_url": "https://static-cdn.jtvnw.net/previews-ttv/live_user_lpl-{width}x{height}.jpg",
                    "tag_ids": [],
                    "tags": [
                        "Esports",
                        "English"
                    ],
                    "is_mature": false
                },
                "liveViewerCount": 14009,
                "viewedCount": 109558663,
                "showOnline": true,
                "onlineDisplay": {
                    "title": "LPL playing League of Legends for 14009 viewers",
                    "showArt": false,
                    "showEmbed": true,
                    "showOverlay": true
                },
                "offlineDisplay": {
                    "title": "lpl",
                    "showArt": false,
                    "showEmbed": false,
                    "showOverlay": false
                },
                "itemType": "streamer",
                "rowName": "Main Display",
                "sortIndex": 17,
                "image": null,
                "overlay": null,
                "customArt": null,
                "link": "https://www.twitch.tv/lpl",
                "componentName": "EmbedContainer",
                "embedName": "TwitchEmbed",
                "embedData": {
                    "channel": "lpl",
                    "elementId": "embed-64106d7f2bd3a"
                },
                "description": null,
                "isGlowStyling": "enabled_if_live"
            },
            {
                "info": {
                    "id": "512710",
                    "name": "Call of Duty: Warzone",
                    "box_art_url": "https://static-cdn.jtvnw.net/ttv-boxart/512710-{width}x{height}.jpg",
                    "igdb_id": "131800"
                },
                "broadcast": {
                    "id": "39408029109",
                    "user_id": "48192899",
                    "user_login": "moonryde",
                    "user_name": "Moonryde",
                    "game_id": "512710",
                    "game_name": "Call of Duty: Warzone",
                    "type": "live",
                    "title": "DOMANI NERFANO IL POMPA | !godwager !Cuffie !Allert !Armi !quest",
                    "viewer_count": 2286,
                    "started_at": "2023-03-14T09:04:46Z",
                    "language": "it",
                    "thumbnail_url": "https://static-cdn.jtvnw.net/previews-ttv/live_user_moonryde-{width}x{height}.jpg",
                    "tag_ids": [],
                    "tags": [
                        "DropAbilitati",
                        "WArzone",
                        "fps",
                        "Italiano"
                    ],
                    "is_mature": false
                },
                "liveViewerCount": 2286,
                "viewedCount": 0,
                "showOnline": true,
                "onlineDisplay": {
                    "title": "Moonryde playing Call of Duty: Warzone for 2286 viewers",
                    "showArt": false,
                    "showEmbed": true,
                    "showOverlay": true
                },
                "offlineDisplay": {
                    "title": "Call of Duty: Warzone",
                    "showArt": false,
                    "showEmbed": false,
                    "showOverlay": false
                },
                "itemType": "game",
                "rowName": "Main Display",
                "sortIndex": 0,
                "image": null,
                "overlay": "https://gamersx-prod-prod-us-west-1-storage.s3.us-west-1.amazonaws.com/o_wv_Msd0G.jpg",
                "customArt": null,
                "link": "https://www.twitch.tv/directory/game/Call of Duty: Warzone",
                "componentName": "EmbedContainer",
                "embedName": "TwitchEmbed",
                "embedData": {
                    "channel": "moonryde",
                    "elementId": "embed-64106d7fbac9c"
                },
                "description": null,
                "isGlowStyling": "enabled_if_live"
            },
            {
                "info": {
                    "id": "31239503",
                    "login": "esl_csgo",
                    "display_name": "ESL_CSGO",
                    "type": "",
                    "broadcaster_type": "partner",
                    "description": "Home of everything Counter-Strike",
                    "profile_image_url": "https://static-cdn.jtvnw.net/jtv_user_pictures/1975b18f-fa7d-443f-b191-fba08f92f3a2-profile_image-300x300.jpeg",
                    "offline_image_url": "https://static-cdn.jtvnw.net/jtv_user_pictures/22dfe613-f7ab-47a5-8a0a-145f56d08c7e-channel_offline_image-1920x1080.png",
                    "view_count": 770388686,
                    "created_at": "2012-06-11T13:36:21Z"
                },
                "broadcast": {
                    "id": "39405586869",
                    "user_id": "31239503",
                    "user_login": "esl_csgo",
                    "user_name": "ESL_CSGO",
                    "game_id": "32399",
                    "game_name": "Counter-Strike: Global Offensive",
                    "type": "live",
                    "title": "HIGLIGHTS: Eternal Fire vs MIBR - ESL Pro League 17 - Group A",
                    "viewer_count": 1588,
                    "started_at": "2023-03-13T11:45:36Z",
                    "language": "en",
                    "thumbnail_url": "https://static-cdn.jtvnw.net/previews-ttv/live_user_esl_csgo-{width}x{height}.jpg",
                    "tag_ids": [],
                    "tags": null,
                    "is_mature": false
                },
                "liveViewerCount": 1588,
                "viewedCount": 770388686,
                "showOnline": true,
                "onlineDisplay": {
                    "title": "ESL_CSGO playing Counter-Strike: Global Offensive for 1588 viewers",
                    "showArt": false,
                    "showEmbed": true,
                    "showOverlay": true
                },
                "offlineDisplay": {
                    "title": "esl_csgo",
                    "showArt": false,
                    "showEmbed": false,
                    "showOverlay": false
                },
                "itemType": "streamer",
                "rowName": "Main Display",
                "sortIndex": -25,
                "image": null,
                "overlay": "https://gamersx-prod-prod-us-west-1-storage.s3.us-west-1.amazonaws.com/yZidW-Asp4.png",
                "customArt": null,
                "link": "https://www.twitch.tv/esl_csgo",
                "componentName": "EmbedContainer",
                "embedName": "TwitchEmbed",
                "embedData": {
                    "channel": "esl_csgo",
                    "elementId": "embed-64106d7e57604"
                },
                "description": null,
                "isGlowStyling": "enabled_if_live"
            }
        ]
    },
    {
        "title": "Top Streams by Genre",
        "sortIndex": -75,
        "componentName": "Parallax",
        "onGamersXtv": false,
        "channels": [
            {
                "info": {
                    "id": "516575",
                    "name": "VALORANT",
                    "box_art_url": "https://static-cdn.jtvnw.net/ttv-boxart/516575-{width}x{height}.jpg",
                    "igdb_id": "126459"
                },
                "broadcast": {
                    "id": "40149049303",
                    "user_id": "50988750",
                    "user_login": "stylishnoob4",
                    "user_name": "stylishnoob4",
                    "game_id": "516575",
                    "game_name": "VALORANT",
                    "type": "live",
                    "title": "フェイドってだれですかVALORANT教えてください",
                    "viewer_count": 25420,
                    "started_at": "2023-03-14T06:37:49Z",
                    "language": "ja",
                    "thumbnail_url": "https://static-cdn.jtvnw.net/previews-ttv/live_user_stylishnoob4-{width}x{height}.jpg",
                    "tag_ids": [],
                    "tags": null,
                    "is_mature": false
                },
                "liveViewerCount": 25420,
                "viewedCount": 0,
                "showOnline": true,
                "onlineDisplay": {
                    "title": "stylishnoob4 playing VALORANT for 25420 viewers",
                    "showArt": false,
                    "showEmbed": true,
                    "showOverlay": true
                },
                "offlineDisplay": {
                    "title": "VALORANT",
                    "showArt": false,
                    "showEmbed": false,
                    "showOverlay": false
                },
                "itemType": "game",
                "rowName": "Top Streams by Genre",
                "sortIndex": 0,
                "image": null,
                "overlay": "https://gamersx-prod-prod-us-west-1-storage.s3.us-west-1.amazonaws.com/EqoYJEJkn0.png",
                "customArt": "https://gamersx-prod-prod-us-west-1-storage.s3.us-west-1.amazonaws.com/v3-cDD7L3U.png",
                "link": "https://www.twitch.tv/directory/game/VALORANT",
                "componentName": "EmbedContainer",
                "embedName": "TwitchEmbed",
                "embedData": {
                    "channel": "stylishnoob4",
                    "elementId": "embed-64106d7fd1f8d"
                },
                "description": null,
                "isGlowStyling": "enabled_if_live"
            },
            {
                "info": {
                    "id": "509658",
                    "name": "Just Chatting",
                    "box_art_url": "https://static-cdn.jtvnw.net/ttv-boxart/509658-{width}x{height}.jpg",
                    "igdb_id": ""
                },
                "broadcast": {
                    "id": "40149780071",
                    "user_id": "702754423",
                    "user_login": "vo_ine",
                    "user_name": "아이네_",
                    "game_id": "509658",
                    "game_name": "Just Chatting",
                    "type": "live",
                    "title": "나 요술봉 샀따!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!",
                    "viewer_count": 10880,
                    "started_at": "2023-03-14T11:53:06Z",
                    "language": "ko",
                    "thumbnail_url": "https://static-cdn.jtvnw.net/previews-ttv/live_user_vo_ine-{width}x{height}.jpg",
                    "tag_ids": [],
                    "tags": [
                        "이세계아이돌",
                        "버튜버",
                        "이세돌",
                        "데친숙주나물",
                        "갑자기노래부름",
                        "둘기",
                        "보리보리쌀",
                        "한국어"
                    ],
                    "is_mature": false
                },
                "liveViewerCount": 10880,
                "viewedCount": 0,
                "showOnline": true,
                "onlineDisplay": {
                    "title": "아이네_ playing Just Chatting for 10880 viewers",
                    "showArt": false,
                    "showEmbed": true,
                    "showOverlay": true
                },
                "offlineDisplay": {
                    "title": "Just Chatting",
                    "showArt": false,
                    "showEmbed": false,
                    "showOverlay": false
                },
                "itemType": "game",
                "rowName": "Top Streams by Genre",
                "sortIndex": 0,
                "image": null,
                "overlay": "https://gamersx-prod-prod-us-west-1-storage.s3.us-west-1.amazonaws.com/paD6UDhSlZ.jpg",
                "customArt": "https://gamersx-prod-prod-us-west-1-storage.s3.us-west-1.amazonaws.com/d0la8n9uGF.jpg",
                "link": "https://www.twitch.tv/directory/game/Just Chatting",
                "componentName": "EmbedContainer",
                "embedName": "TwitchEmbed",
                "embedData": {
                    "channel": "vo_ine",
                    "elementId": "embed-64106d804b435"
                },
                "description": null,
                "isGlowStyling": "enabled_if_live"
            },
            {
                "info": {
                    "id": "32399",
                    "name": "Counter-Strike: Global Offensive",
                    "box_art_url": "https://static-cdn.jtvnw.net/ttv-boxart/32399_IGDB-{width}x{height}.jpg",
                    "igdb_id": "1372"
                },
                "broadcast": {
                    "id": "39407971141",
                    "user_id": "268731435",
                    "user_login": "cct_en",
                    "user_name": "CCT_EN",
                    "game_id": "32399",
                    "game_name": "Counter-Strike: Global Offensive",
                    "type": "live",
                    "title": "COPENHAGEN FLAMES vs ASTRALIS TALENT 🔹 CCT North Europe –  Playoffs 🏆 Online Series 4",
                    "viewer_count": 5305,
                    "started_at": "2023-03-14T08:28:47Z",
                    "language": "en",
                    "thumbnail_url": "https://static-cdn.jtvnw.net/previews-ttv/live_user_cct_en-{width}x{height}.jpg",
                    "tag_ids": [],
                    "tags": [
                        "Esports",
                        "English"
                    ],
                    "is_mature": false
                },
                "liveViewerCount": 5305,
                "viewedCount": 0,
                "showOnline": true,
                "onlineDisplay": {
                    "title": "CCT_EN playing Counter-Strike: Global Offensive for 5305 viewers",
                    "showArt": false,
                    "showEmbed": true,
                    "showOverlay": true
                },
                "offlineDisplay": {
                    "title": "Counter-Strike: Global Offensive",
                    "showArt": false,
                    "showEmbed": false,
                    "showOverlay": false
                },
                "itemType": "game",
                "rowName": "Top Streams by Genre",
                "sortIndex": 0,
                "image": null,
                "overlay": "https://gamersx-prod-prod-us-west-1-storage.s3.us-west-1.amazonaws.com/B1SYGpLyXR.png",
                "customArt": "https://gamersx-prod-prod-us-west-1-storage.s3.us-west-1.amazonaws.com/MKIlrhuwzy.png",
                "link": "https://www.twitch.tv/directory/game/Counter-Strike: Global Offensive",
                "componentName": "EmbedContainer",
                "embedName": "TwitchEmbed",
                "embedData": {
                    "channel": "cct_en",
                    "elementId": "embed-64106d803472a"
                },
                "description": null,
                "isGlowStyling": "enabled_if_live"
            },
            {
                "info": {
                    "id": "21779",
                    "name": "League of Legends",
                    "box_art_url": "https://static-cdn.jtvnw.net/ttv-boxart/21779-{width}x{height}.jpg",
                    "igdb_id": "115"
                },
                "broadcast": {
                    "id": "40149358023",
                    "user_id": "45839490",
                    "user_login": "alfrea",
                    "user_name": "らいじん",
                    "game_id": "21779",
                    "game_name": "League of Legends",
                    "type": "live",
                    "title": "疲れるまでLOLする",
                    "viewer_count": 2122,
                    "started_at": "2023-03-14T09:23:49Z",
                    "language": "ja",
                    "thumbnail_url": "https://static-cdn.jtvnw.net/previews-ttv/live_user_alfrea-{width}x{height}.jpg",
                    "tag_ids": [],
                    "tags": [
                        "ネタバレ禁止",
                        "アドバイス禁止",
                        "日本語"
                    ],
                    "is_mature": false
                },
                "liveViewerCount": 2122,
                "viewedCount": 0,
                "showOnline": true,
                "onlineDisplay": {
                    "title": "らいじん playing League of Legends for 2122 viewers",
                    "showArt": false,
                    "showEmbed": true,
                    "showOverlay": true
                },
                "offlineDisplay": {
                    "title": "League of Legends",
                    "showArt": false,
                    "showEmbed": false,
                    "showOverlay": false
                },
                "itemType": "game",
                "rowName": "Top Streams by Genre",
                "sortIndex": 16,
                "image": null,
                "overlay": "https://gamersx-prod-prod-us-west-1-storage.s3.us-west-1.amazonaws.com/ULl2PZlXxB.jpg",
                "customArt": "https://gamersx-prod-prod-us-west-1-storage.s3.us-west-1.amazonaws.com/DyDzQJNqjM.jpg",
                "link": "https://www.twitch.tv/directory/game/League of Legends",
                "componentName": "EmbedContainer",
                "embedName": "TwitchEmbed",
                "embedData": {
                    "channel": "alfrea",
                    "elementId": "embed-64106d7fe9ee2"
                },
                "description": null,
                "isGlowStyling": "enabled_if_live"
            },
            {
                "info": {
                    "id": "27471",
                    "name": "Minecraft",
                    "box_art_url": "https://static-cdn.jtvnw.net/ttv-boxart/27471_IGDB-{width}x{height}.jpg",
                    "igdb_id": "121"
                },
                "broadcast": {
                    "id": "40563482888",
                    "user_id": "69239046",
                    "user_login": "iskall85",
                    "user_name": "iskall85",
                    "game_id": "27471",
                    "game_name": "Minecraft",
                    "type": "live",
                    "title": "8.2 OUT - Today we VAULT | Vault Hunters SMP EP 73 | !vh118 | !vhpatreon | !vods | !patch",
                    "viewer_count": 1710,
                    "started_at": "2023-03-14T09:15:46Z",
                    "language": "en",
                    "thumbnail_url": "https://static-cdn.jtvnw.net/previews-ttv/live_user_iskall85-{width}x{height}.jpg",
                    "tag_ids": [],
                    "tags": [
                        "English"
                    ],
                    "is_mature": false
                },
                "liveViewerCount": 1710,
                "viewedCount": 0,
                "showOnline": true,
                "onlineDisplay": {
                    "title": "iskall85 playing Minecraft for 1710 viewers",
                    "showArt": false,
                    "showEmbed": true,
                    "showOverlay": true
                },
                "offlineDisplay": {
                    "title": "Minecraft",
                    "showArt": false,
                    "showEmbed": false,
                    "showOverlay": false
                },
                "itemType": "game",
                "rowName": "Top Streams by Genre",
                "sortIndex": 0,
                "image": null,
                "overlay": "https://gamersx-prod-prod-us-west-1-storage.s3.us-west-1.amazonaws.com/yfd286jm-E.jpg",
                "customArt": "https://gamersx-prod-prod-us-west-1-storage.s3.us-west-1.amazonaws.com/asQUwGIoBQ.png",
                "link": "https://www.twitch.tv/directory/game/Minecraft",
                "componentName": "EmbedContainer",
                "embedName": "TwitchEmbed",
                "embedData": {
                    "channel": "iskall85",
                    "elementId": "embed-64106d800f20b"
                },
                "description": null,
                "isGlowStyling": "enabled_if_live"
            }
        ]
    },
    {
        "title": "Top 12 Streamers",
        "sortIndex": -60,
        "componentName": "NumberedRow",
        "onGamersXtv": true,
        "channels": [
            {
                "info": {
                    "auditDetails": null,
                    "brandingSettings": null,
                    "contentDetails": null,
                    "contentOwnerDetails": null,
                    "conversionPings": null,
                    "etag": "ElE1mdR-pS3bQKFbAkfJ_YBB9Vg",
                    "id": "UCWxlUwW9BgGISaakjGM37aw",
                    "kind": "youtube#channel",
                    "localizations": [],
                    "snippet": {
                        "country": "US",
                        "customUrl": "@valkyrae",
                        "defaultLanguage": null,
                        "description": "hi",
                        "localized": {
                            "description": "hi",
                            "title": "Valkyrae"
                        },
                        "publishedAt": "2014-09-07T03:46:16Z",
                        "thumbnails": {
                            "default": {
                                "height": 88,
                                "url": "https://yt3.ggpht.com/0Qu_iyV5XEUmy7MBd46TqRzsMuTED6M5zvunG8W6GjOrRn3pgs-BSTLREWopbkmzQIC66R_FPDs=s88-c-k-c0x00ffffff-no-rj",
                                "width": 88
                            },
                            "high": {
                                "height": 800,
                                "url": "https://yt3.ggpht.com/0Qu_iyV5XEUmy7MBd46TqRzsMuTED6M5zvunG8W6GjOrRn3pgs-BSTLREWopbkmzQIC66R_FPDs=s800-c-k-c0x00ffffff-no-rj",
                                "width": 800
                            },
                            "maxres": null,
                            "medium": {
                                "height": 240,
                                "url": "https://yt3.ggpht.com/0Qu_iyV5XEUmy7MBd46TqRzsMuTED6M5zvunG8W6GjOrRn3pgs-BSTLREWopbkmzQIC66R_FPDs=s240-c-k-c0x00ffffff-no-rj",
                                "width": 240
                            },
                            "standard": null
                        },
                        "title": "Valkyrae"
                    },
                    "statistics": {
                        "commentCount": null,
                        "hiddenSubscriberCount": false,
                        "subscriberCount": "3840000",
                        "videoCount": "556",
                        "viewCount": "369639186"
                    },
                    "status": null,
                    "topicDetails": null
                },
                "broadcast": null,
                "liveViewerCount": 0,
                "viewedCount": 369639186,
                "showOnline": false,
                "onlineDisplay": {
                    "title": "Valkyrae",
                    "showArt": false,
                    "showEmbed": true,
                    "showOverlay": true
                },
                "offlineDisplay": {
                    "title": "Valkyrae",
                    "showArt": false,
                    "showEmbed": false,
                    "showOverlay": true
                },
                "itemType": "channel",
                "rowName": "Top 12 Streamers",
                "sortIndex": 4,
                "image": null,
                "overlay": "https://gamersx-prod-prod-us-west-1-storage.s3.us-west-1.amazonaws.com/QPHesT8eRZ.jpg",
                "customArt": null,
                "link": "https://www.youtube.com/c/@valkyrae",
                "componentName": "EmbedContainer",
                "embedName": "YouTubeEmbed",
                "embedData": null,
                "description": null,
                "isGlowStyling": "enabled_if_live"
            },
            {
                "info": {
                    "id": "51496027",
                    "login": "loltyler1",
                    "display_name": "loltyler1",
                    "type": "",
                    "broadcaster_type": "partner",
                    "description": "Season 6 Challenger | Most REFORMED Player NA | #1 Draven World ",
                    "profile_image_url": "https://static-cdn.jtvnw.net/jtv_user_pictures/f3591dbe4ee3d94b-profile_image-300x300.png",
                    "offline_image_url": "https://static-cdn.jtvnw.net/jtv_user_pictures/2d5a63b7-1297-481d-b050-d1b58e10945d-channel_offline_image-1920x1080.png",
                    "view_count": 301327063,
                    "created_at": "2013-11-14T00:27:41Z"
                },
                "broadcast": null,
                "liveViewerCount": 0,
                "viewedCount": 301327063,
                "showOnline": false,
                "onlineDisplay": {
                    "title": "loltyler1",
                    "showArt": false,
                    "showEmbed": true,
                    "showOverlay": true
                },
                "offlineDisplay": {
                    "title": "loltyler1",
                    "showArt": false,
                    "showEmbed": false,
                    "showOverlay": true
                },
                "itemType": "streamer",
                "rowName": "Top 12 Streamers",
                "sortIndex": 3,
                "image": null,
                "overlay": "https://gamersx-prod-prod-us-west-1-storage.s3.us-west-1.amazonaws.com/mnMCU6Kkm-.jpg",
                "customArt": null,
                "link": "https://www.twitch.tv/loltyler1",
                "componentName": "EmbedContainer",
                "embedName": "TwitchEmbed",
                "embedData": null,
                "description": null,
                "isGlowStyling": "enabled_if_live"
            },
            {
                "info": {
                    "id": "44445592",
                    "login": "pokimane",
                    "display_name": "pokimane",
                    "type": "",
                    "broadcaster_type": "partner",
                    "description": "Welcome to my channel! :) I play a variety of games, chat, stream IRL & more! I love interacting with my chat & making others happy, so come say hi!",
                    "profile_image_url": "https://static-cdn.jtvnw.net/jtv_user_pictures/6cd4de40-1a83-46c7-aea5-3bd73f90e7e4-profile_image-300x300.png",
                    "offline_image_url": "https://static-cdn.jtvnw.net/jtv_user_pictures/843a8764-a86d-4716-8ebd-ea50c6fd081a-channel_offline_image-1920x1080.jpeg",
                    "view_count": 219814873,
                    "created_at": "2013-06-07T14:25:22Z"
                },
                "broadcast": null,
                "liveViewerCount": 0,
                "viewedCount": 219814873,
                "showOnline": false,
                "onlineDisplay": {
                    "title": "pokimane",
                    "showArt": false,
                    "showEmbed": true,
                    "showOverlay": true
                },
                "offlineDisplay": {
                    "title": "pokimane",
                    "showArt": false,
                    "showEmbed": false,
                    "showOverlay": true
                },
                "itemType": "streamer",
                "rowName": "Top 12 Streamers",
                "sortIndex": 12,
                "image": null,
                "overlay": "https://gamersx-prod-prod-us-west-1-storage.s3.us-west-1.amazonaws.com/_fhrf5aCvn.jpg",
                "customArt": null,
                "link": "https://www.twitch.tv/pokimane",
                "componentName": "EmbedContainer",
                "embedName": "TwitchEmbed",
                "embedData": null,
                "description": null,
                "isGlowStyling": "enabled_if_live"
            },
            {
                "info": {
                    "auditDetails": null,
                    "brandingSettings": null,
                    "contentDetails": null,
                    "contentOwnerDetails": null,
                    "conversionPings": null,
                    "etag": "Tckz2iTWAWBNubhIJQq1qFGYA5I",
                    "id": "UCNF0LEQ2abMr0PAX3cfkAMg",
                    "kind": "youtube#channel",
                    "localizations": [],
                    "snippet": {
                        "country": "US",
                        "customUrl": "@drlupo",
                        "defaultLanguage": null,
                        "description": "Welcome to the channel! My name is DrLupo, and I'm a father, husband, and gamer from the Midwest. I typically play FPS games, but I try to dabble in a variety of games! This is the destination for all exclusive content from me, whether pre-recorded or live!\n\nWanna see what's going on when I'm not here? \n\nTwitter - @DrLupo\nInstagram - @DrLupo\nTikTok - @DrLupo\n\nBe sure to subscribe to see all future videos!",
                        "localized": {
                            "description": "Welcome to the channel! My name is DrLupo, and I'm a father, husband, and gamer from the Midwest. I typically play FPS games, but I try to dabble in a variety of games! This is the destination for all exclusive content from me, whether pre-recorded or live!\n\nWanna see what's going on when I'm not here? \n\nTwitter - @DrLupo\nInstagram - @DrLupo\nTikTok - @DrLupo\n\nBe sure to subscribe to see all future videos!",
                            "title": "DrLupo"
                        },
                        "publishedAt": "2013-10-05T19:08:47Z",
                        "thumbnails": {
                            "default": {
                                "height": 88,
                                "url": "https://yt3.ggpht.com/ytc/AL5GRJX8_7rsmpbncPSVZLs4LZkGme83sNp44Rd0h5P1LQ=s88-c-k-c0x00ffffff-no-rj",
                                "width": 88
                            },
                            "high": {
                                "height": 800,
                                "url": "https://yt3.ggpht.com/ytc/AL5GRJX8_7rsmpbncPSVZLs4LZkGme83sNp44Rd0h5P1LQ=s800-c-k-c0x00ffffff-no-rj",
                                "width": 800
                            },
                            "maxres": null,
                            "medium": {
                                "height": 240,
                                "url": "https://yt3.ggpht.com/ytc/AL5GRJX8_7rsmpbncPSVZLs4LZkGme83sNp44Rd0h5P1LQ=s240-c-k-c0x00ffffff-no-rj",
                                "width": 240
                            },
                            "standard": null
                        },
                        "title": "DrLupo"
                    },
                    "statistics": {
                        "commentCount": null,
                        "hiddenSubscriberCount": false,
                        "subscriberCount": "1870000",
                        "videoCount": "1821",
                        "viewCount": "176670649"
                    },
                    "status": null,
                    "topicDetails": null
                },
                "broadcast": null,
                "liveViewerCount": 0,
                "viewedCount": 176670649,
                "showOnline": false,
                "onlineDisplay": {
                    "title": "DrLupo",
                    "showArt": false,
                    "showEmbed": true,
                    "showOverlay": true
                },
                "offlineDisplay": {
                    "title": "DrLupo",
                    "showArt": false,
                    "showEmbed": false,
                    "showOverlay": true
                },
                "itemType": "channel",
                "rowName": "Top 12 Streamers",
                "sortIndex": 7,
                "image": null,
                "overlay": "https://gamersx-prod-prod-us-west-1-storage.s3.us-west-1.amazonaws.com/oVM7Z1v9q3.jpg",
                "customArt": null,
                "link": "https://www.youtube.com/c/@drlupo",
                "componentName": "EmbedContainer",
                "embedName": "YouTubeEmbed",
                "embedData": null,
                "description": null,
                "isGlowStyling": "enabled_if_live"
            },
            {
                "info": {
                    "id": "36340781",
                    "login": "tarik",
                    "display_name": "tarik",
                    "type": "",
                    "broadcaster_type": "partner",
                    "description": "ayo. Business Inquiries: tarik@loaded.gg",
                    "profile_image_url": "https://static-cdn.jtvnw.net/jtv_user_pictures/f04d2a14-8d63-4cd5-a469-7ec2cd6e5ce3-profile_image-300x300.png",
                    "offline_image_url": "https://static-cdn.jtvnw.net/jtv_user_pictures/eab243f6-4710-4203-b3db-ee249b6f4b8c-channel_offline_image-1920x1080.jpeg",
                    "view_count": 93331565,
                    "created_at": "2012-09-21T20:43:25Z"
                },
                "broadcast": null,
                "liveViewerCount": 0,
                "viewedCount": 93331565,
                "showOnline": false,
                "onlineDisplay": {
                    "title": "tarik",
                    "showArt": false,
                    "showEmbed": true,
                    "showOverlay": true
                },
                "offlineDisplay": {
                    "title": "tarik",
                    "showArt": false,
                    "showEmbed": false,
                    "showOverlay": true
                },
                "itemType": "streamer",
                "rowName": "Top 12 Streamers",
                "sortIndex": 1,
                "image": null,
                "overlay": "https://gamersx-prod-prod-us-west-1-storage.s3.us-west-1.amazonaws.com/cfT_cizYbq.jpg",
                "customArt": null,
                "link": "https://www.twitch.tv/tarik",
                "componentName": "EmbedContainer",
                "embedName": "TwitchEmbed",
                "embedData": null,
                "description": null,
                "isGlowStyling": "enabled_if_live"
            },
            {
                "info": {
                    "id": "146922206",
                    "login": "tsm_imperialhal",
                    "display_name": "TSM_ImperialHal",
                    "type": "",
                    "broadcaster_type": "partner",
                    "description": "",
                    "profile_image_url": "https://static-cdn.jtvnw.net/jtv_user_pictures/039b8034-2b66-4d30-97ec-15bf08f219d9-profile_image-300x300.png",
                    "offline_image_url": "https://static-cdn.jtvnw.net/jtv_user_pictures/ad80469c-0961-4e18-b514-4042f4fb023b-channel_offline_image-1920x1080.png",
                    "view_count": 70533754,
                    "created_at": "2017-02-04T18:58:28Z"
                },
                "broadcast": null,
                "liveViewerCount": 0,
                "viewedCount": 70533754,
                "showOnline": false,
                "onlineDisplay": {
                    "title": "TSM_ImperialHal",
                    "showArt": false,
                    "showEmbed": true,
                    "showOverlay": true
                },
                "offlineDisplay": {
                    "title": "tsm_imperialhal",
                    "showArt": false,
                    "showEmbed": false,
                    "showOverlay": true
                },
                "itemType": "streamer",
                "rowName": "Top 12 Streamers",
                "sortIndex": 5,
                "image": null,
                "overlay": "https://gamersx-prod-prod-us-west-1-storage.s3.us-west-1.amazonaws.com/Lt0HH7R260.jpg",
                "customArt": null,
                "link": "/streamer/146922206",
                "componentName": "EmbedContainer",
                "embedName": "TwitchEmbed",
                "embedData": null,
                "description": null,
                "isGlowStyling": "enabled_if_live"
            },
            {
                "info": {
                    "id": "81687332",
                    "login": "cloakzy",
                    "display_name": "cloakzy",
                    "type": "",
                    "broadcaster_type": "partner",
                    "description": "Battle Royale Pro Player.",
                    "profile_image_url": "https://static-cdn.jtvnw.net/jtv_user_pictures/320226c6-f422-4baf-8ed2-1be7eb3757e6-profile_image-300x300.png",
                    "offline_image_url": "https://static-cdn.jtvnw.net/jtv_user_pictures/05c63a18-e600-4809-b3f1-91ad0cca4e04-channel_offline_image-1920x1080.jpeg",
                    "view_count": 61541709,
                    "created_at": "2015-02-03T07:50:36Z"
                },
                "broadcast": null,
                "liveViewerCount": 0,
                "viewedCount": 61541709,
                "showOnline": false,
                "onlineDisplay": {
                    "title": "cloakzy",
                    "showArt": false,
                    "showEmbed": true,
                    "showOverlay": true
                },
                "offlineDisplay": {
                    "title": "cloakzy",
                    "showArt": false,
                    "showEmbed": false,
                    "showOverlay": true
                },
                "itemType": "streamer",
                "rowName": "Top 12 Streamers",
                "sortIndex": 10,
                "image": null,
                "overlay": "https://gamersx-prod-prod-us-west-1-storage.s3.us-west-1.amazonaws.com/hYN1SWIOfW.jpg",
                "customArt": null,
                "link": "https://www.twitch.tv/cloakzy",
                "componentName": "EmbedContainer",
                "embedName": "TwitchEmbed",
                "embedData": null,
                "description": null,
                "isGlowStyling": "enabled_if_live"
            },
            {
                "info": {
                    "id": "132230344",
                    "login": "moistcr1tikal",
                    "display_name": "moistcr1tikal",
                    "type": "",
                    "broadcaster_type": "partner",
                    "description": "I make things",
                    "profile_image_url": "https://static-cdn.jtvnw.net/jtv_user_pictures/df6f775d-8794-4cbe-aa85-dbdc72975696-profile_image-300x300.png",
                    "offline_image_url": "https://static-cdn.jtvnw.net/jtv_user_pictures/dfd463f0-1d16-4ac3-9203-155d27ef886c-channel_offline_image-1920x1080.png",
                    "view_count": 59320942,
                    "created_at": "2016-08-15T01:10:43Z"
                },
                "broadcast": null,
                "liveViewerCount": 0,
                "viewedCount": 59320942,
                "showOnline": false,
                "onlineDisplay": {
                    "title": "moistcr1tikal",
                    "showArt": false,
                    "showEmbed": true,
                    "showOverlay": true
                },
                "offlineDisplay": {
                    "title": "moistcr1tikal",
                    "showArt": false,
                    "showEmbed": false,
                    "showOverlay": true
                },
                "itemType": "streamer",
                "rowName": "Top 12 Streamers",
                "sortIndex": 9,
                "image": null,
                "overlay": "https://gamersx-prod-prod-us-west-1-storage.s3.us-west-1.amazonaws.com/_DEBjI2mY4.jpg",
                "customArt": null,
                "link": "https://www.twitch.tv/moistcr1tikal",
                "componentName": "EmbedContainer",
                "embedName": "TwitchEmbed",
                "embedData": null,
                "description": null,
                "isGlowStyling": "enabled_if_live"
            },
            {
                "info": {
                    "id": "83402203",
                    "login": "fuslie",
                    "display_name": "fuslie",
                    "type": "",
                    "broadcaster_type": "",
                    "description": "HI I'M LESLIE! I'M STREAMING OVER AT YOUTUBE NOW :DD",
                    "profile_image_url": "https://static-cdn.jtvnw.net/jtv_user_pictures/517e697a-6768-4834-a703-c326cc1a74b5-profile_image-300x300.png",
                    "offline_image_url": "https://static-cdn.jtvnw.net/jtv_user_pictures/174d4fb5-0c71-46d0-a22a-438264b9e0db-channel_offline_image-1920x1080.png",
                    "view_count": 54179421,
                    "created_at": "2015-02-21T22:39:43Z"
                },
                "broadcast": null,
                "liveViewerCount": 0,
                "viewedCount": 54179421,
                "showOnline": false,
                "onlineDisplay": {
                    "title": "fuslie",
                    "showArt": false,
                    "showEmbed": true,
                    "showOverlay": true
                },
                "offlineDisplay": {
                    "title": "fuslie",
                    "showArt": false,
                    "showEmbed": false,
                    "showOverlay": true
                },
                "itemType": "streamer",
                "rowName": "Top 12 Streamers",
                "sortIndex": 6,
                "image": null,
                "overlay": "https://gamersx-prod-prod-us-west-1-storage.s3.us-west-1.amazonaws.com/tUhu8AWwYP.jpg",
                "customArt": null,
                "link": "https://www.twitch.tv/fuslie",
                "componentName": "EmbedContainer",
                "embedName": "TwitchEmbed",
                "embedData": null,
                "description": null,
                "isGlowStyling": "enabled_if_live"
            },
            {
                "info": {
                    "id": "70225218",
                    "login": "tenz",
                    "display_name": "TenZ",
                    "type": "",
                    "broadcaster_type": "partner",
                    "description": "Professional VALORANT Player for Sentinels",
                    "profile_image_url": "https://static-cdn.jtvnw.net/jtv_user_pictures/17a3e618-d192-4870-82e5-b55082289a2e-profile_image-300x300.png",
                    "offline_image_url": "https://static-cdn.jtvnw.net/jtv_user_pictures/8061fbad-5c94-4a1e-886d-2aae69347fab-channel_offline_image-1920x1080.jpeg",
                    "view_count": 52820165,
                    "created_at": "2014-08-30T08:21:22Z"
                },
                "broadcast": null,
                "liveViewerCount": 0,
                "viewedCount": 52820165,
                "showOnline": false,
                "onlineDisplay": {
                    "title": "TenZ",
                    "showArt": false,
                    "showEmbed": true,
                    "showOverlay": true
                },
                "offlineDisplay": {
                    "title": "tenz",
                    "showArt": false,
                    "showEmbed": false,
                    "showOverlay": true
                },
                "itemType": "streamer",
                "rowName": "Top 12 Streamers",
                "sortIndex": 2,
                "image": null,
                "overlay": "https://gamersx-prod-prod-us-west-1-storage.s3.us-west-1.amazonaws.com/R3HztA6GOU.jpg",
                "customArt": null,
                "link": "https://www.twitch.tv/tenz",
                "componentName": "EmbedContainer",
                "embedName": "TwitchEmbed",
                "embedData": null,
                "description": null,
                "isGlowStyling": "enabled_if_live"
            },
            {
                "info": {
                    "id": "45302947",
                    "login": "iitztimmy",
                    "display_name": "iiTzTimmy",
                    "type": "",
                    "broadcaster_type": "partner",
                    "description": "Where dreams are made",
                    "profile_image_url": "https://static-cdn.jtvnw.net/jtv_user_pictures/07580fe8-4509-4cbd-84b6-31732234e98c-profile_image-300x300.png",
                    "offline_image_url": "https://static-cdn.jtvnw.net/jtv_user_pictures/c249593f-388b-4621-88b1-39444b7781d6-channel_offline_image-1920x1080.png",
                    "view_count": 46151009,
                    "created_at": "2013-06-28T04:15:32Z"
                },
                "broadcast": null,
                "liveViewerCount": 0,
                "viewedCount": 46151009,
                "showOnline": false,
                "onlineDisplay": {
                    "title": "iiTzTimmy",
                    "showArt": false,
                    "showEmbed": true,
                    "showOverlay": true
                },
                "offlineDisplay": {
                    "title": "iitztimmy",
                    "showArt": false,
                    "showEmbed": false,
                    "showOverlay": true
                },
                "itemType": "streamer",
                "rowName": "Top 12 Streamers",
                "sortIndex": 11,
                "image": null,
                "overlay": "https://gamersx-prod-prod-us-west-1-storage.s3.us-west-1.amazonaws.com/HBIp8XjRQ9.jpg",
                "customArt": null,
                "link": "/streamer/45302947",
                "componentName": "EmbedContainer",
                "embedName": "TwitchEmbed",
                "embedData": null,
                "description": null,
                "isGlowStyling": "enabled_if_live"
            },
            {
                "info": {
                    "id": "160677372",
                    "login": "closer",
                    "display_name": "Closer",
                    "type": "",
                    "broadcaster_type": "partner",
                    "description": "Professional League of Legends Jungler for 100Thieves",
                    "profile_image_url": "https://static-cdn.jtvnw.net/jtv_user_pictures/b9b4ec2f-2196-4152-ad06-dbe2ad690019-profile_image-300x300.png",
                    "offline_image_url": "https://static-cdn.jtvnw.net/jtv_user_pictures/045b130e-3977-49c7-9476-796484e592f6-channel_offline_image-1920x1080.jpeg",
                    "view_count": 8473179,
                    "created_at": "2017-06-19T00:50:00Z"
                },
                "broadcast": null,
                "liveViewerCount": 0,
                "viewedCount": 8473179,
                "showOnline": false,
                "onlineDisplay": {
                    "title": "Closer",
                    "showArt": false,
                    "showEmbed": true,
                    "showOverlay": true
                },
                "offlineDisplay": {
                    "title": "closer",
                    "showArt": false,
                    "showEmbed": false,
                    "showOverlay": true
                },
                "itemType": "streamer",
                "rowName": "Top 12 Streamers",
                "sortIndex": 8,
                "image": null,
                "overlay": "https://gamersx-prod-prod-us-west-1-storage.s3.us-west-1.amazonaws.com/Pb7Vr9-MrF.jpg",
                "customArt": null,
                "link": "https://www.twitch.tv/closer",
                "componentName": "EmbedContainer",
                "embedName": "TwitchEmbed",
                "embedData": null,
                "description": null,
                "isGlowStyling": "enabled_if_live"
            }
        ]
    },
    {
        "title": "Promotional Level",
        "sortIndex": -50,
        "componentName": "FullWidthImagery",
        "onGamersXtv": false,
        "channels": [
            {
                "info": {
                    "id": "21779",
                    "name": "League of Legends",
                    "box_art_url": "https://static-cdn.jtvnw.net/ttv-boxart/21779-{width}x{height}.jpg",
                    "igdb_id": "115"
                },
                "broadcast": {
                    "id": "40149190215",
                    "user_id": "124425627",
                    "user_login": "lpl",
                    "user_name": "LPL",
                    "game_id": "21779",
                    "game_name": "League of Legends",
                    "type": "live",
                    "title": "RA VS. UP | NIP VS. IG - Week 9 Day 2 | LPL Spring Split (2023)",
                    "viewer_count": 14009,
                    "started_at": "2023-03-14T07:58:26Z",
                    "language": "en",
                    "thumbnail_url": "https://static-cdn.jtvnw.net/previews-ttv/live_user_lpl-{width}x{height}.jpg",
                    "tag_ids": [],
                    "tags": [
                        "Esports",
                        "English"
                    ],
                    "is_mature": false
                },
                "liveViewerCount": 14009,
                "viewedCount": 0,
                "showOnline": true,
                "onlineDisplay": {
                    "title": "LPL playing League of Legends for 14009 viewers",
                    "showArt": false,
                    "showEmbed": true,
                    "showOverlay": true
                },
                "offlineDisplay": {
                    "title": "League of Legends",
                    "showArt": false,
                    "showEmbed": false,
                    "showOverlay": false
                },
                "itemType": "game",
                "rowName": "Promotional Level",
                "sortIndex": 0,
                "image": null,
                "overlay": "https://gamersx-prod-prod-us-west-1-storage.s3.us-west-1.amazonaws.com/JBzULgK8gT.jpg",
                "customArt": "https://gamersx-prod-prod-us-west-1-storage.s3.us-west-1.amazonaws.com/5WxFgw5vMM.jpeg",
                "link": "https://www.twitch.tv/directory/game/League of Legends",
                "componentName": "EmbedContainer",
                "embedName": "TwitchEmbed",
                "embedData": {
                    "channel": "lpl",
                    "elementId": "embed-64106d8273f2c"
                },
                "description": null,
                "isGlowStyling": "enabled_if_live"
            },
            {
                "info": {
                    "id": "511224",
                    "name": "Apex Legends",
                    "box_art_url": "https://static-cdn.jtvnw.net/ttv-boxart/511224-{width}x{height}.jpg",
                    "igdb_id": "114795"
                },
                "broadcast": {
                    "id": "40563523544",
                    "user_id": "128856353",
                    "user_login": "mande",
                    "user_name": "Mande",
                    "game_id": "511224",
                    "game_name": "Apex Legends",
                    "type": "live",
                    "title": "WAKE UP!😤HUGE DRAMA😤ONLY WINS😤NEVER LIED😤 RANK 1 TODAY😤 !yt !tiktok !blitz",
                    "viewer_count": 7354,
                    "started_at": "2023-03-14T09:55:31Z",
                    "language": "en",
                    "thumbnail_url": "https://static-cdn.jtvnw.net/previews-ttv/live_user_mande-{width}x{height}.jpg",
                    "tag_ids": [],
                    "tags": [
                        "English"
                    ],
                    "is_mature": false
                },
                "liveViewerCount": 7354,
                "viewedCount": 0,
                "showOnline": true,
                "onlineDisplay": {
                    "title": "Mande playing Apex Legends for 7354 viewers",
                    "showArt": false,
                    "showEmbed": true,
                    "showOverlay": true
                },
                "offlineDisplay": {
                    "title": "Apex Legends",
                    "showArt": false,
                    "showEmbed": false,
                    "showOverlay": false
                },
                "itemType": "game",
                "rowName": "Promotional Level",
                "sortIndex": 0,
                "image": null,
                "overlay": "https://gamersx-prod-prod-us-west-1-storage.s3.us-west-1.amazonaws.com/9E-EunYRWk.jpg",
                "customArt": "https://gamersx-prod-prod-us-west-1-storage.s3.us-west-1.amazonaws.com/VhJ_MXf2ed.jpeg",
                "link": "https://www.twitch.tv/directory/game/Apex Legends",
                "componentName": "EmbedContainer",
                "embedName": "TwitchEmbed",
                "embedData": {
                    "channel": "mande",
                    "elementId": "embed-64106d825ce8c"
                },
                "description": null,
                "isGlowStyling": "enabled_if_live"
            },
            {
                "info": {
                    "id": "1678052513",
                    "name": "Call of Duty: Modern Warfare II",
                    "box_art_url": "https://static-cdn.jtvnw.net/ttv-boxart/1678052513-{width}x{height}.jpg",
                    "igdb_id": "200165"
                },
                "broadcast": {
                    "id": "41960043819",
                    "user_id": "31688366",
                    "user_login": "symfuhny",
                    "user_name": "Symfuhny",
                    "game_id": "1678052513",
                    "game_name": "Call of Duty: Modern Warfare II",
                    "type": "live",
                    "title": "BIG WARZONE UPDATE TOMORROW? IRIDESCENT TOP 250 TODAY???? BECOMING THE BEST RANKED PLAYER IN THE WORLD !hellofresh",
                    "viewer_count": 1438,
                    "started_at": "2023-03-14T12:32:51Z",
                    "language": "en",
                    "thumbnail_url": "https://static-cdn.jtvnw.net/previews-ttv/live_user_symfuhny-{width}x{height}.jpg",
                    "tag_ids": [],
                    "tags": [
                        "English"
                    ],
                    "is_mature": false
                },
                "liveViewerCount": 1438,
                "viewedCount": 0,
                "showOnline": true,
                "onlineDisplay": {
                    "title": "Symfuhny playing Call of Duty: Modern Warfare II for 1438 viewers",
                    "showArt": false,
                    "showEmbed": true,
                    "showOverlay": true
                },
                "offlineDisplay": {
                    "title": "Call of Duty: Modern Warfare II",
                    "showArt": false,
                    "showEmbed": false,
                    "showOverlay": false
                },
                "itemType": "game",
                "rowName": "Promotional Level",
                "sortIndex": 0,
                "image": null,
                "overlay": "https://gamersx-prod-prod-us-west-1-storage.s3.us-west-1.amazonaws.com/DE1ubrt_CT.jpg",
                "customArt": "https://gamersx-prod-prod-us-west-1-storage.s3.us-west-1.amazonaws.com/9oWXHIbUKL.jpeg",
                "link": "https://www.twitch.tv/directory/game/Call of Duty: Modern Warfare II",
                "componentName": "EmbedContainer",
                "embedName": "TwitchEmbed",
                "embedData": {
                    "channel": "symfuhny",
                    "elementId": "embed-64106d823defc"
                },
                "description": null,
                "isGlowStyling": "enabled_if_live"
            },
            {
                "info": {
                    "auditDetails": null,
                    "brandingSettings": null,
                    "contentDetails": null,
                    "contentOwnerDetails": null,
                    "conversionPings": null,
                    "etag": "zra49NLUX3ApRhw85rqNgdJhCSM",
                    "id": "UCcJL2ld6kxy_nuV1u7PVQ0g",
                    "kind": "youtube#channel",
                    "localizations": [],
                    "snippet": {
                        "country": "US",
                        "customUrl": "@drdisrespect",
                        "defaultLanguage": null,
                        "description": "The Official Dr Disrespect YouTube Channel",
                        "localized": {
                            "description": "The Official Dr Disrespect YouTube Channel",
                            "title": "DrDisRespect"
                        },
                        "publishedAt": "2010-01-05T17:09:56Z",
                        "thumbnails": {
                            "default": {
                                "height": 88,
                                "url": "https://yt3.ggpht.com/tvkeak3Sfk6blV7sMIO6CjhUMSTmNTSgiBF2NOaj7V0xuZPEvkmARxgKtCUFpyONef-LS1ZW=s88-c-k-c0x00ffffff-no-rj",
                                "width": 88
                            },
                            "high": {
                                "height": 800,
                                "url": "https://yt3.ggpht.com/tvkeak3Sfk6blV7sMIO6CjhUMSTmNTSgiBF2NOaj7V0xuZPEvkmARxgKtCUFpyONef-LS1ZW=s800-c-k-c0x00ffffff-no-rj",
                                "width": 800
                            },
                            "maxres": null,
                            "medium": {
                                "height": 240,
                                "url": "https://yt3.ggpht.com/tvkeak3Sfk6blV7sMIO6CjhUMSTmNTSgiBF2NOaj7V0xuZPEvkmARxgKtCUFpyONef-LS1ZW=s240-c-k-c0x00ffffff-no-rj",
                                "width": 240
                            },
                            "standard": null
                        },
                        "title": "DrDisRespect"
                    },
                    "statistics": {
                        "commentCount": null,
                        "hiddenSubscriberCount": false,
                        "subscriberCount": "4290000",
                        "videoCount": "816",
                        "viewCount": "377170784"
                    },
                    "status": null,
                    "topicDetails": null
                },
                "broadcast": null,
                "liveViewerCount": 0,
                "viewedCount": 377170784,
                "showOnline": false,
                "onlineDisplay": {
                    "title": "DrDisRespect",
                    "showArt": false,
                    "showEmbed": true,
                    "showOverlay": true
                },
                "offlineDisplay": {
                    "title": "DrDisRespect",
                    "showArt": false,
                    "showEmbed": false,
                    "showOverlay": true
                },
                "itemType": "channel",
                "rowName": "Promotional Level",
                "sortIndex": 0,
                "image": null,
                "overlay": "https://gamersx-prod-prod-us-west-1-storage.s3.us-west-1.amazonaws.com/eDQj31LgDs.jpg",
                "customArt": "https://gamersx-prod-prod-us-west-1-storage.s3.us-west-1.amazonaws.com/xWC8yJv9b8.jpg_large",
                "link": "https://www.youtube.com/c/@drdisrespect",
                "componentName": "EmbedContainer",
                "embedName": "YouTubeEmbed",
                "embedData": null,
                "description": null,
                "isGlowStyling": "enabled_if_live"
            }
        ]
    },
    {
        "title": "Pro League Play",
        "sortIndex": -25,
        "componentName": "ClassicLg",
        "onGamersXtv": true,
        "channels": [
            {
                "info": {
                    "id": "124425627",
                    "login": "lpl",
                    "display_name": "LPL",
                    "type": "",
                    "broadcaster_type": "partner",
                    "description": "Official Twitch Channel of the League of Legends Pro League (LPL). For more information, schedules and stats head to Lolesports.com. #CrazyIsOurGame",
                    "profile_image_url": "https://static-cdn.jtvnw.net/jtv_user_pictures/8111888b-17e2-43b6-ac5f-10bb3451b130-profile_image-300x300.png",
                    "offline_image_url": "https://static-cdn.jtvnw.net/jtv_user_pictures/8f757f88-176d-4e83-930b-bceff3a39a62-channel_offline_image-1920x1080.png",
                    "view_count": 109558663,
                    "created_at": "2016-05-16T18:37:03Z"
                },
                "broadcast": {
                    "id": "40149190215",
                    "user_id": "124425627",
                    "user_login": "lpl",
                    "user_name": "LPL",
                    "game_id": "21779",
                    "game_name": "League of Legends",
                    "type": "live",
                    "title": "RA VS. UP | NIP VS. IG - Week 9 Day 2 | LPL Spring Split (2023)",
                    "viewer_count": 14009,
                    "started_at": "2023-03-14T07:58:26Z",
                    "language": "en",
                    "thumbnail_url": "https://static-cdn.jtvnw.net/previews-ttv/live_user_lpl-{width}x{height}.jpg",
                    "tag_ids": [],
                    "tags": [
                        "Esports",
                        "English"
                    ],
                    "is_mature": false
                },
                "liveViewerCount": 14009,
                "viewedCount": 109558663,
                "showOnline": true,
                "onlineDisplay": {
                    "title": "LPL playing League of Legends for 14009 viewers",
                    "showArt": false,
                    "showEmbed": true,
                    "showOverlay": true
                },
                "offlineDisplay": {
                    "title": "lpl",
                    "showArt": false,
                    "showEmbed": false,
                    "showOverlay": false
                },
                "itemType": "streamer",
                "rowName": "Pro League Play",
                "sortIndex": null,
                "image": null,
                "overlay": "https://gamersx-prod-prod-us-west-1-storage.s3.us-west-1.amazonaws.com/lhAOfNAOiD.jpg",
                "customArt": null,
                "link": "https://www.twitch.tv/lpl",
                "componentName": "EmbedContainer",
                "embedName": "TwitchEmbed",
                "embedData": {
                    "channel": "lpl",
                    "elementId": "embed-64106d842f518"
                },
                "description": null,
                "isGlowStyling": "enabled_if_live"
            },
            {
                "info": {
                    "id": "31239503",
                    "login": "esl_csgo",
                    "display_name": "ESL_CSGO",
                    "type": "",
                    "broadcaster_type": "partner",
                    "description": "Home of everything Counter-Strike",
                    "profile_image_url": "https://static-cdn.jtvnw.net/jtv_user_pictures/1975b18f-fa7d-443f-b191-fba08f92f3a2-profile_image-300x300.jpeg",
                    "offline_image_url": "https://static-cdn.jtvnw.net/jtv_user_pictures/22dfe613-f7ab-47a5-8a0a-145f56d08c7e-channel_offline_image-1920x1080.png",
                    "view_count": 770388686,
                    "created_at": "2012-06-11T13:36:21Z"
                },
                "broadcast": {
                    "id": "39405586869",
                    "user_id": "31239503",
                    "user_login": "esl_csgo",
                    "user_name": "ESL_CSGO",
                    "game_id": "32399",
                    "game_name": "Counter-Strike: Global Offensive",
                    "type": "live",
                    "title": "HIGLIGHTS: Eternal Fire vs MIBR - ESL Pro League 17 - Group A",
                    "viewer_count": 1588,
                    "started_at": "2023-03-13T11:45:36Z",
                    "language": "en",
                    "thumbnail_url": "https://static-cdn.jtvnw.net/previews-ttv/live_user_esl_csgo-{width}x{height}.jpg",
                    "tag_ids": [],
                    "tags": null,
                    "is_mature": false
                },
                "liveViewerCount": 1588,
                "viewedCount": 770388686,
                "showOnline": true,
                "onlineDisplay": {
                    "title": "ESL_CSGO playing Counter-Strike: Global Offensive for 1588 viewers",
                    "showArt": false,
                    "showEmbed": true,
                    "showOverlay": true
                },
                "offlineDisplay": {
                    "title": "esl_csgo",
                    "showArt": false,
                    "showEmbed": false,
                    "showOverlay": true
                },
                "itemType": "streamer",
                "rowName": "Pro League Play",
                "sortIndex": null,
                "image": null,
                "overlay": "https://gamersx-prod-prod-us-west-1-storage.s3.us-west-1.amazonaws.com/p6lqcoBYQN.png",
                "customArt": null,
                "link": "/streamer/31239503",
                "componentName": "EmbedContainer",
                "embedName": "TwitchEmbed",
                "embedData": {
                    "channel": "esl_csgo",
                    "elementId": "embed-64106d8386e37"
                },
                "description": null,
                "isGlowStyling": "enabled_if_live"
            },
            {
                "info": {
                    "id": "22346597",
                    "login": "lvpes",
                    "display_name": "LVPes",
                    "type": "",
                    "broadcaster_type": "partner",
                    "description": "El canal LVPes emite las competiciones oficiales de League of Legends: Superliga, LEC, MSI o Worlds. Suscríbete para acceder a contenidos exclusivos, sorteos, premios y un montón de ventajas. ",
                    "profile_image_url": "https://static-cdn.jtvnw.net/jtv_user_pictures/lvpes-profile_image-b9a288b926641fe4-300x300.png",
                    "offline_image_url": "",
                    "view_count": 240624287,
                    "created_at": "2011-05-11T10:25:51Z"
                },
                "broadcast": {
                    "id": "46702559612",
                    "user_id": "22346597",
                    "user_login": "lvpes",
                    "user_name": "LVPes",
                    "game_id": "21779",
                    "game_name": "League of Legends",
                    "type": "live",
                    "title": "[REBROADCAST] SEMANA 1 - DÍA 3 - LEC - PRIMAVERA 2023 - LEAGUE OF LEGENDS",
                    "viewer_count": 582,
                    "started_at": "2023-03-14T08:00:05Z",
                    "language": "es",
                    "thumbnail_url": "https://static-cdn.jtvnw.net/previews-ttv/live_user_lvpes-{width}x{height}.jpg",
                    "tag_ids": [],
                    "tags": [
                        "Esports",
                        "Español"
                    ],
                    "is_mature": false
                },
                "liveViewerCount": 582,
                "viewedCount": 240624287,
                "showOnline": true,
                "onlineDisplay": {
                    "title": "LVPes playing League of Legends for 582 viewers",
                    "showArt": false,
                    "showEmbed": true,
                    "showOverlay": true
                },
                "offlineDisplay": {
                    "title": "lvpes",
                    "showArt": false,
                    "showEmbed": false,
                    "showOverlay": false
                },
                "itemType": "streamer",
                "rowName": "Pro League Play",
                "sortIndex": null,
                "image": null,
                "overlay": "https://gamersx-prod-prod-us-west-1-storage.s3.us-west-1.amazonaws.com/Tyrd8rHQFG.png",
                "customArt": null,
                "link": "https://www.twitch.tv/lvpes",
                "componentName": "EmbedContainer",
                "embedName": "TwitchEmbed",
                "embedData": {
                    "channel": "lvpes",
                    "elementId": "embed-64106d8403acf"
                },
                "description": null,
                "isGlowStyling": "enabled_if_live"
            }
        ]
    },
    {
        "title": "Popular",
        "sortIndex": -20,
        "componentName": "ClassicLg",
        "onGamersXtv": true,
        "channels": [
            {
                "info": {
                    "id": "491931",
                    "name": "Escape from Tarkov",
                    "box_art_url": "https://static-cdn.jtvnw.net/ttv-boxart/491931_IGDB-{width}x{height}.jpg",
                    "igdb_id": "15536"
                },
                "broadcast": {
                    "id": "40563413736",
                    "user_id": "63304572",
                    "user_login": "glorious_e",
                    "user_name": "Glorious_E",
                    "game_id": "491931",
                    "game_name": "Escape from Tarkov",
                    "type": "live",
                    "title": "!Turkey - !Giveaway | !Memes | !PvPGuide | !CyberghostVPN | @glorious_e on Socials",
                    "viewer_count": 4254,
                    "started_at": "2023-03-14T08:02:50Z",
                    "language": "en",
                    "thumbnail_url": "https://static-cdn.jtvnw.net/previews-ttv/live_user_glorious_e-{width}x{height}.jpg",
                    "tag_ids": [],
                    "tags": [
                        "PVP",
                        "Vibes",
                        "Chill",
                        "Smile",
                        "PVE",
                        "W",
                        "English"
                    ],
                    "is_mature": true
                },
                "liveViewerCount": 4254,
                "viewedCount": 0,
                "showOnline": true,
                "onlineDisplay": {
                    "title": "Glorious_E playing Escape from Tarkov for 4254 viewers",
                    "showArt": false,
                    "showEmbed": true,
                    "showOverlay": true
                },
                "offlineDisplay": {
                    "title": "Escape from Tarkov",
                    "showArt": false,
                    "showEmbed": false,
                    "showOverlay": false
                },
                "itemType": "game",
                "rowName": "Popular",
                "sortIndex": 0,
                "image": null,
                "overlay": "https://gamersx-prod-prod-us-west-1-storage.s3.us-west-1.amazonaws.com/qTUIO-mMRL.jpg",
                "customArt": null,
                "link": "https://www.twitch.tv/directory/game/Escape from Tarkov",
                "componentName": "EmbedContainer",
                "embedName": "TwitchEmbed",
                "embedData": {
                    "channel": "glorious_e",
                    "elementId": "embed-64106d84603bf"
                },
                "description": null,
                "isGlowStyling": "enabled_if_live"
            },
            {
                "info": {
                    "id": "513143",
                    "name": "Teamfight Tactics",
                    "box_art_url": "https://static-cdn.jtvnw.net/ttv-boxart/513143-{width}x{height}.jpg",
                    "igdb_id": "120176"
                },
                "broadcast": {
                    "id": "40149797767",
                    "user_id": "162323092",
                    "user_login": "gearbaby1010",
                    "user_name": "花輪同學",
                    "game_id": "513143",
                    "game_name": "Teamfight Tactics",
                    "type": "live",
                    "title": "今天要奪回失去的東西！！！  3/14",
                    "viewer_count": 3307,
                    "started_at": "2023-03-14T11:57:49Z",
                    "language": "zh",
                    "thumbnail_url": "https://static-cdn.jtvnw.net/previews-ttv/live_user_gearbaby1010-{width}x{height}.jpg",
                    "tag_ids": [],
                    "tags": [
                        "中文"
                    ],
                    "is_mature": false
                },
                "liveViewerCount": 3307,
                "viewedCount": 0,
                "showOnline": true,
                "onlineDisplay": {
                    "title": "花輪同學 playing Teamfight Tactics for 3307 viewers",
                    "showArt": false,
                    "showEmbed": true,
                    "showOverlay": true
                },
                "offlineDisplay": {
                    "title": "Teamfight Tactics",
                    "showArt": false,
                    "showEmbed": false,
                    "showOverlay": false
                },
                "itemType": "game",
                "rowName": "Popular",
                "sortIndex": 0,
                "image": null,
                "overlay": "https://gamersx-prod-prod-us-west-1-storage.s3.us-west-1.amazonaws.com/v4SzwbLuux.jpg",
                "customArt": null,
                "link": "https://www.twitch.tv/directory/game/Teamfight Tactics",
                "componentName": "EmbedContainer",
                "embedName": "TwitchEmbed",
                "embedData": {
                    "channel": "gearbaby1010",
                    "elementId": "embed-64106d84bd98b"
                },
                "description": null,
                "isGlowStyling": "enabled_if_live"
            },
            {
                "info": {
                    "id": "18122",
                    "name": "World of Warcraft",
                    "box_art_url": "https://static-cdn.jtvnw.net/ttv-boxart/18122-{width}x{height}.jpg",
                    "igdb_id": "123"
                },
                "broadcast": {
                    "id": "40563399048",
                    "user_id": "56489971",
                    "user_login": "metashi12",
                    "user_name": "metashi12",
                    "game_id": "18122",
                    "game_name": "World of Warcraft",
                    "type": "live",
                    "title": "[CHARITY]DEATH=DELETE+GIVEAWAY !märz !spende !liebe !giveaway !wer !hc #Werbung #Charity",
                    "viewer_count": 2645,
                    "started_at": "2023-03-14T07:45:33Z",
                    "language": "de",
                    "thumbnail_url": "https://static-cdn.jtvnw.net/previews-ttv/live_user_metashi12-{width}x{height}.jpg",
                    "tag_ids": [],
                    "tags": [
                        "Giveaway",
                        "Free",
                        "DropsAktiviert",
                        "Drops",
                        "Deutsch"
                    ],
                    "is_mature": true
                },
                "liveViewerCount": 2645,
                "viewedCount": 0,
                "showOnline": true,
                "onlineDisplay": {
                    "title": "metashi12 playing World of Warcraft for 2645 viewers",
                    "showArt": false,
                    "showEmbed": true,
                    "showOverlay": true
                },
                "offlineDisplay": {
                    "title": "World of Warcraft",
                    "showArt": false,
                    "showEmbed": false,
                    "showOverlay": false
                },
                "itemType": "game",
                "rowName": "Popular",
                "sortIndex": 0,
                "image": null,
                "overlay": "https://gamersx-prod-prod-us-west-1-storage.s3.us-west-1.amazonaws.com/bL2fWB_OoX.jpg",
                "customArt": null,
                "link": "https://www.twitch.tv/directory/game/World of Warcraft",
                "componentName": "EmbedContainer",
                "embedName": "TwitchEmbed",
                "embedData": {
                    "channel": "metashi12",
                    "elementId": "embed-64106d84d5333"
                },
                "description": null,
                "isGlowStyling": "enabled_if_live"
            },
            {
                "info": {
                    "id": "33214",
                    "name": "Fortnite",
                    "box_art_url": "https://static-cdn.jtvnw.net/ttv-boxart/33214-{width}x{height}.jpg",
                    "igdb_id": "1905"
                },
                "broadcast": {
                    "id": "41068162217",
                    "user_id": "70661496",
                    "user_login": "chica",
                    "user_name": "Chica",
                    "game_id": "33214",
                    "game_name": "Fortnite",
                    "type": "live",
                    "title": "GRINDING CROWN VICTORY ROYALES! CODE CHICA IN THE ITEM SHOP #AD",
                    "viewer_count": 1847,
                    "started_at": "2023-03-14T11:48:55Z",
                    "language": "en",
                    "thumbnail_url": "https://static-cdn.jtvnw.net/previews-ttv/live_user_chica-{width}x{height}.jpg",
                    "tag_ids": [],
                    "tags": [
                        "SPANISH",
                        "LBGTQ",
                        "ESPAÑOl",
                        "English"
                    ],
                    "is_mature": false
                },
                "liveViewerCount": 1847,
                "viewedCount": 0,
                "showOnline": true,
                "onlineDisplay": {
                    "title": "Chica playing Fortnite for 1847 viewers",
                    "showArt": false,
                    "showEmbed": true,
                    "showOverlay": true
                },
                "offlineDisplay": {
                    "title": "Fortnite",
                    "showArt": false,
                    "showEmbed": false,
                    "showOverlay": false
                },
                "itemType": "game",
                "rowName": "Popular",
                "sortIndex": 0,
                "image": null,
                "overlay": "https://gamersx-prod-prod-us-west-1-storage.s3.us-west-1.amazonaws.com/3BPXXxWY8a.jpg",
                "customArt": null,
                "link": "https://www.twitch.tv/directory/game/Fortnite",
                "componentName": "EmbedContainer",
                "embedName": "TwitchEmbed",
                "embedData": {
                    "channel": "chica",
                    "elementId": "embed-64106d84a672e"
                },
                "description": null,
                "isGlowStyling": "enabled_if_live"
            },
            {
                "info": {
                    "id": "30921",
                    "name": "Rocket League",
                    "box_art_url": "https://static-cdn.jtvnw.net/ttv-boxart/30921-{width}x{height}.jpg",
                    "igdb_id": "11198"
                },
                "broadcast": {
                    "id": "39408277605",
                    "user_id": "117323707",
                    "user_login": "fuury_off",
                    "user_name": "FuuRy_Off",
                    "game_id": "30921",
                    "game_name": "Rocket League",
                    "type": "live",
                    "title": "!HoopsCup |  🔥ROAD TO SSL🔥 ||  👻 !CyberGhost VPN 84% || [ 💻PROMO !Intel ] || !BE4BET 🎁 || !HoopsCup",
                    "viewer_count": 1679,
                    "started_at": "2023-03-14T11:23:12Z",
                    "language": "fr",
                    "thumbnail_url": "https://static-cdn.jtvnw.net/previews-ttv/live_user_fuury_off-{width}x{height}.jpg",
                    "tag_ids": [],
                    "tags": [
                        "Français"
                    ],
                    "is_mature": true
                },
                "liveViewerCount": 1679,
                "viewedCount": 0,
                "showOnline": true,
                "onlineDisplay": {
                    "title": "FuuRy_Off playing Rocket League for 1679 viewers",
                    "showArt": false,
                    "showEmbed": true,
                    "showOverlay": true
                },
                "offlineDisplay": {
                    "title": "Rocket League",
                    "showArt": false,
                    "showEmbed": false,
                    "showOverlay": false
                },
                "itemType": "game",
                "rowName": "Popular",
                "sortIndex": 0,
                "image": null,
                "overlay": "https://gamersx-prod-prod-us-west-1-storage.s3.us-west-1.amazonaws.com/LkqPsfrZF5.jpg",
                "customArt": null,
                "link": "https://www.twitch.tv/directory/game/Rocket League",
                "componentName": "EmbedContainer",
                "embedName": "TwitchEmbed",
                "embedData": {
                    "channel": "fuury_off",
                    "elementId": "embed-64106d8518a75"
                },
                "description": null,
                "isGlowStyling": "enabled_if_live"
            },
            {
                "info": {
                    "id": "460630",
                    "name": "Tom Clancy's Rainbow Six Siege",
                    "box_art_url": "https://static-cdn.jtvnw.net/ttv-boxart/460630_IGDB-{width}x{height}.jpg",
                    "igdb_id": "7360"
                },
                "broadcast": {
                    "id": "40149476503",
                    "user_id": "136255354",
                    "user_login": "franciscoow",
                    "user_name": "franciscoow",
                    "game_id": "460630",
                    "game_name": "Tom Clancy's Rainbow Six Siege",
                    "type": "live",
                    "title": "見なきゃRJL！の練習配信 TEAM Wokka From Crazy Raccoon シスコ視点(初心者)",
                    "viewer_count": 614,
                    "started_at": "2023-03-14T10:15:15Z",
                    "language": "ja",
                    "thumbnail_url": "https://static-cdn.jtvnw.net/previews-ttv/live_user_franciscoow-{width}x{height}.jpg",
                    "tag_ids": [],
                    "tags": [
                        "日本語"
                    ],
                    "is_mature": false
                },
                "liveViewerCount": 614,
                "viewedCount": 0,
                "showOnline": true,
                "onlineDisplay": {
                    "title": "franciscoow playing Tom Clancy's Rainbow Six Siege for 614 viewers",
                    "showArt": false,
                    "showEmbed": true,
                    "showOverlay": true
                },
                "offlineDisplay": {
                    "title": "Tom Clancy's Rainbow Six Siege",
                    "showArt": false,
                    "showEmbed": false,
                    "showOverlay": false
                },
                "itemType": "game",
                "rowName": "Popular",
                "sortIndex": 0,
                "image": null,
                "overlay": "https://gamersx-prod-prod-us-west-1-storage.s3.us-west-1.amazonaws.com/XtmSRC18sa.jpg",
                "customArt": null,
                "link": "https://www.twitch.tv/directory/game/Tom Clancy's Rainbow Six Siege",
                "componentName": "EmbedContainer",
                "embedName": "TwitchEmbed",
                "embedData": {
                    "channel": "franciscoow",
                    "elementId": "embed-64106d8448b7a"
                },
                "description": null,
                "isGlowStyling": "enabled_if_live"
            },
            {
                "info": {
                    "id": "670867987",
                    "name": "Pokémon Scarlet/Violet",
                    "box_art_url": "https://static-cdn.jtvnw.net/ttv-boxart/670867987-{width}x{height}.jpg",
                    "igdb_id": ""
                },
                "broadcast": {
                    "id": "40149601191",
                    "user_id": "194071615",
                    "user_login": "ahn_mori",
                    "user_name": "안모리",
                    "game_id": "670867987",
                    "game_name": "Pokémon Scarlet/Violet",
                    "type": "live",
                    "title": "싱글벙글",
                    "viewer_count": 280,
                    "started_at": "2023-03-14T11:01:19Z",
                    "language": "ko",
                    "thumbnail_url": "https://static-cdn.jtvnw.net/previews-ttv/live_user_ahn_mori-{width}x{height}.jpg",
                    "tag_ids": [],
                    "tags": [
                        "한국어"
                    ],
                    "is_mature": false
                },
                "liveViewerCount": 280,
                "viewedCount": 0,
                "showOnline": true,
                "onlineDisplay": {
                    "title": "안모리 playing Pokémon Scarlet/Violet for 280 viewers",
                    "showArt": false,
                    "showEmbed": true,
                    "showOverlay": true
                },
                "offlineDisplay": {
                    "title": "Pokémon Scarlet/Violet",
                    "showArt": false,
                    "showEmbed": false,
                    "showOverlay": false
                },
                "itemType": "game",
                "rowName": "Popular",
                "sortIndex": 0,
                "image": null,
                "overlay": "https://gamersx-prod-prod-us-west-1-storage.s3.us-west-1.amazonaws.com/E_jLORvoqC.jpg",
                "customArt": null,
                "link": "https://www.twitch.tv/directory/game/Pokémon Scarlet/Violet",
                "componentName": "EmbedContainer",
                "embedName": "TwitchEmbed",
                "embedData": {
                    "channel": "ahn_mori",
                    "elementId": "embed-64106d848f581"
                },
                "description": null,
                "isGlowStyling": "enabled_if_live"
            },
            {
                "info": {
                    "id": "460316",
                    "name": "Brawlhalla",
                    "box_art_url": "https://static-cdn.jtvnw.net/ttv-boxart/460316_IGDB-{width}x{height}.jpg",
                    "igdb_id": "10233"
                },
                "broadcast": {
                    "id": "41959630827",
                    "user_id": "270320501",
                    "user_login": "wubz",
                    "user_name": "wubz",
                    "game_id": "460316",
                    "game_name": "Brawlhalla",
                    "type": "live",
                    "title": "vc with godly",
                    "viewer_count": 98,
                    "started_at": "2023-03-14T08:21:48Z",
                    "language": "en",
                    "thumbnail_url": "https://static-cdn.jtvnw.net/previews-ttv/live_user_wubz-{width}x{height}.jpg",
                    "tag_ids": [],
                    "tags": [
                        "English"
                    ],
                    "is_mature": false
                },
                "liveViewerCount": 98,
                "viewedCount": 0,
                "showOnline": true,
                "onlineDisplay": {
                    "title": "wubz playing Brawlhalla for 98 viewers",
                    "showArt": false,
                    "showEmbed": true,
                    "showOverlay": true
                },
                "offlineDisplay": {
                    "title": "Brawlhalla",
                    "showArt": false,
                    "showEmbed": false,
                    "showOverlay": false
                },
                "itemType": "game",
                "rowName": "Popular",
                "sortIndex": 0,
                "image": null,
                "overlay": "https://gamersx-prod-prod-us-west-1-storage.s3.us-west-1.amazonaws.com/mgte5MNkmz.jpg",
                "customArt": null,
                "link": "https://www.twitch.tv/directory/game/Brawlhalla",
                "componentName": "EmbedContainer",
                "embedName": "TwitchEmbed",
                "embedData": {
                    "channel": "wubz",
                    "elementId": "embed-64106d850238d"
                },
                "description": null,
                "isGlowStyling": "enabled_if_live"
            }
        ]
    },
    {
        "title": "Celebrating Women Streamers",
        "sortIndex": -15,
        "componentName": "ClassicVertical",
        "onGamersXtv": false,
        "channels": []
    },
    {
        "title": "Top Games",
        "sortIndex": 0,
        "componentName": "ClassicMd",
        "onGamersXtv": false,
        "channels": [
            {
                "info": {
                    "id": "516575",
                    "name": "VALORANT",
                    "box_art_url": "https://static-cdn.jtvnw.net/ttv-boxart/516575-{width}x{height}.jpg",
                    "igdb_id": "126459"
                },
                "broadcast": {
                    "id": "40149049303",
                    "user_id": "50988750",
                    "user_login": "stylishnoob4",
                    "user_name": "stylishnoob4",
                    "game_id": "516575",
                    "game_name": "VALORANT",
                    "type": "live",
                    "title": "フェイドってだれですかVALORANT教えてください",
                    "viewer_count": 25420,
                    "started_at": "2023-03-14T06:37:49Z",
                    "language": "ja",
                    "thumbnail_url": "https://static-cdn.jtvnw.net/previews-ttv/live_user_stylishnoob4-{width}x{height}.jpg",
                    "tag_ids": [],
                    "tags": null,
                    "is_mature": false
                },
                "liveViewerCount": 25420,
                "viewedCount": 0,
                "showOnline": true,
                "onlineDisplay": {
                    "title": "stylishnoob4 playing VALORANT for 25420 viewers",
                    "showArt": false,
                    "showEmbed": true,
                    "showOverlay": true
                },
                "offlineDisplay": {
                    "title": "VALORANT",
                    "showArt": false,
                    "showEmbed": false,
                    "showOverlay": false
                },
                "itemType": "game",
                "rowName": "Top Games",
                "sortIndex": 0,
                "image": null,
                "overlay": "https://gamersx-prod-prod-us-west-1-storage.s3.us-west-1.amazonaws.com/8XPPKw5i_y.jpg",
                "customArt": null,
                "link": "https://www.twitch.tv/directory/game/VALORANT",
                "componentName": "EmbedContainer",
                "embedName": "TwitchEmbed",
                "embedData": {
                    "channel": "stylishnoob4",
                    "elementId": "embed-64106d865d2c2"
                },
                "description": null,
                "isGlowStyling": "enabled_if_live"
            },
            {
                "info": {
                    "id": "513181",
                    "name": "Genshin Impact",
                    "box_art_url": "https://static-cdn.jtvnw.net/ttv-boxart/513181_IGDB-{width}x{height}.jpg",
                    "igdb_id": "119277"
                },
                "broadcast": {
                    "id": "40149686519",
                    "user_id": "44525650",
                    "user_login": "k4sen",
                    "user_name": "k4sen",
                    "game_id": "513181",
                    "game_name": "Genshin Impact",
                    "type": "live",
                    "title": "今日の原神",
                    "viewer_count": 8232,
                    "started_at": "2023-03-14T11:26:05Z",
                    "language": "ja",
                    "thumbnail_url": "https://static-cdn.jtvnw.net/previews-ttv/live_user_k4sen-{width}x{height}.jpg",
                    "tag_ids": [],
                    "tags": [
                        "日本語",
                        "ネタバレNG",
                        "ネタバレ注意",
                        "リークに触れるもの皆BAN",
                        "Drops有効"
                    ],
                    "is_mature": false
                },
                "liveViewerCount": 8232,
                "viewedCount": 0,
                "showOnline": true,
                "onlineDisplay": {
                    "title": "k4sen playing Genshin Impact for 8232 viewers",
                    "showArt": false,
                    "showEmbed": true,
                    "showOverlay": true
                },
                "offlineDisplay": {
                    "title": "Genshin Impact",
                    "showArt": false,
                    "showEmbed": false,
                    "showOverlay": false
                },
                "itemType": "game",
                "rowName": "Top Games",
                "sortIndex": 0,
                "image": null,
                "overlay": "https://gamersx-prod-prod-us-west-1-storage.s3.us-west-1.amazonaws.com/EcFPVILIn7.jpg",
                "customArt": null,
                "link": "https://www.twitch.tv/directory/game/Genshin Impact",
                "componentName": "EmbedContainer",
                "embedName": "TwitchEmbed",
                "embedData": {
                    "channel": "k4sen",
                    "elementId": "embed-64106d867c837"
                },
                "description": null,
                "isGlowStyling": "enabled_if_live"
            },
            {
                "info": {
                    "id": "490100",
                    "name": "Lost Ark",
                    "box_art_url": "https://static-cdn.jtvnw.net/ttv-boxart/490100-{width}x{height}.jpg",
                    "igdb_id": "26128"
                },
                "broadcast": {
                    "id": "40149360199",
                    "user_id": "133923936",
                    "user_login": "2chamcham2",
                    "user_name": "탬탬버린",
                    "game_id": "490100",
                    "game_name": "Lost Ark",
                    "type": "live",
                    "title": "하이용~~ ٩(●'▿'●)۶ *",
                    "viewer_count": 5994,
                    "started_at": "2023-03-14T09:24:58Z",
                    "language": "ko",
                    "thumbnail_url": "https://static-cdn.jtvnw.net/previews-ttv/live_user_2chamcham2-{width}x{height}.jpg",
                    "tag_ids": [],
                    "tags": [
                        "한국어"
                    ],
                    "is_mature": false
                },
                "liveViewerCount": 5994,
                "viewedCount": 0,
                "showOnline": true,
                "onlineDisplay": {
                    "title": "탬탬버린 playing Lost Ark for 5994 viewers",
                    "showArt": false,
                    "showEmbed": true,
                    "showOverlay": true
                },
                "offlineDisplay": {
                    "title": "Lost Ark",
                    "showArt": false,
                    "showEmbed": false,
                    "showOverlay": false
                },
                "itemType": "game",
                "rowName": "Top Games",
                "sortIndex": 0,
                "image": null,
                "overlay": "https://gamersx-prod-prod-us-west-1-storage.s3.us-west-1.amazonaws.com/Ig4D8N3pA1.jpg",
                "customArt": null,
                "link": "https://www.twitch.tv/directory/game/Lost Ark",
                "componentName": "EmbedContainer",
                "embedName": "TwitchEmbed",
                "embedData": {
                    "channel": "2chamcham2",
                    "elementId": "embed-64106d8619ee6"
                },
                "description": null,
                "isGlowStyling": "enabled_if_live"
            },
            {
                "info": {
                    "id": "512953",
                    "name": "ELDEN RING",
                    "box_art_url": "https://static-cdn.jtvnw.net/ttv-boxart/512953_IGDB-{width}x{height}.jpg",
                    "igdb_id": "119133"
                },
                "broadcast": {
                    "id": "46702594428",
                    "user_id": "38284441",
                    "user_login": "sakor_",
                    "user_name": "Sakor_",
                    "game_id": "512953",
                    "game_name": "ELDEN RING",
                    "type": "live",
                    "title": "Salut les bros c'est Sakor - 18h30 Hebdo Esport sur Solary !Discord !RS",
                    "viewer_count": 1154,
                    "started_at": "2023-03-14T08:49:47Z",
                    "language": "fr",
                    "thumbnail_url": "https://static-cdn.jtvnw.net/previews-ttv/live_user_sakor_-{width}x{height}.jpg",
                    "tag_ids": [],
                    "tags": [
                        "Francais",
                        "Solary",
                        "30seconds",
                        "cest",
                        "une",
                        "belle",
                        "perf",
                        "Mage",
                        "Noir",
                        "Français"
                    ],
                    "is_mature": false
                },
                "liveViewerCount": 1154,
                "viewedCount": 0,
                "showOnline": true,
                "onlineDisplay": {
                    "title": "Sakor_ playing ELDEN RING for 1154 viewers",
                    "showArt": false,
                    "showEmbed": true,
                    "showOverlay": true
                },
                "offlineDisplay": {
                    "title": "ELDEN RING",
                    "showArt": false,
                    "showEmbed": false,
                    "showOverlay": false
                },
                "itemType": "game",
                "rowName": "Top Games",
                "sortIndex": 0,
                "image": null,
                "overlay": "https://gamersx-prod-prod-us-west-1-storage.s3.us-west-1.amazonaws.com/MAZhFFXoHC.jpg",
                "customArt": null,
                "link": "/game/512953",
                "componentName": "EmbedContainer",
                "embedName": "TwitchEmbed",
                "embedData": {
                    "channel": "sakor_",
                    "elementId": "embed-64106d862e8f5"
                },
                "description": null,
                "isGlowStyling": "enabled_if_live"
            },
            {
                "info": {
                    "id": "1115711128",
                    "name": "PlateUp!",
                    "box_art_url": "https://static-cdn.jtvnw.net/ttv-boxart/1115711128_IGDB-{width}x{height}.jpg",
                    "igdb_id": "151353"
                },
                "broadcast": {
                    "id": "40563412568",
                    "user_id": "43582520",
                    "user_login": "perrick",
                    "user_name": "Perrick",
                    "game_id": "1115711128",
                    "game_name": "PlateUp!",
                    "type": "live",
                    "title": "Bisschen kochen - !Cyberghost #Werbung | !pc !Loupedeck",
                    "viewer_count": 345,
                    "started_at": "2023-03-14T08:01:49Z",
                    "language": "de",
                    "thumbnail_url": "https://static-cdn.jtvnw.net/previews-ttv/live_user_perrick-{width}x{height}.jpg",
                    "tag_ids": [],
                    "tags": [
                        "Anime",
                        "Salz",
                        "Deutsch"
                    ],
                    "is_mature": true
                },
                "liveViewerCount": 345,
                "viewedCount": 0,
                "showOnline": true,
                "onlineDisplay": {
                    "title": "Perrick playing PlateUp! for 345 viewers",
                    "showArt": false,
                    "showEmbed": true,
                    "showOverlay": true
                },
                "offlineDisplay": {
                    "title": "PlateUp!",
                    "showArt": false,
                    "showEmbed": false,
                    "showOverlay": false
                },
                "itemType": "game",
                "rowName": "Top Games",
                "sortIndex": 0,
                "image": null,
                "overlay": "https://gamersx-prod-prod-us-west-1-storage.s3.us-west-1.amazonaws.com/r5tOYGP4Ve.jpg",
                "customArt": null,
                "link": "https://www.twitch.tv/directory/game/PlateUp!",
                "componentName": "EmbedContainer",
                "embedName": "TwitchEmbed",
                "embedData": {
                    "channel": "perrick",
                    "elementId": "embed-64106d8603e89"
                },
                "description": null,
                "isGlowStyling": "enabled_if_live"
            },
            {
                "info": {
                    "id": "102007682",
                    "name": "God of War Ragnarök",
                    "box_art_url": "https://static-cdn.jtvnw.net/ttv-boxart/102007682_IGDB-{width}x{height}.jpg",
                    "igdb_id": "112875"
                },
                "broadcast": {
                    "id": "48131477373",
                    "user_id": "854540084",
                    "user_login": "cwells2423",
                    "user_name": "cwells2423",
                    "game_id": "102007682",
                    "game_name": "God of War Ragnarök",
                    "type": "live",
                    "title": "God of war 🔥👊COME IN GANGYS👽👾",
                    "viewer_count": 50,
                    "started_at": "2023-03-14T12:42:12Z",
                    "language": "en",
                    "thumbnail_url": "https://static-cdn.jtvnw.net/previews-ttv/live_user_cwells2423-{width}x{height}.jpg",
                    "tag_ids": [],
                    "tags": [
                        "englIsh",
                        "codMobile",
                        "firststream",
                        "English"
                    ],
                    "is_mature": true
                },
                "liveViewerCount": 50,
                "viewedCount": 0,
                "showOnline": true,
                "onlineDisplay": {
                    "title": "cwells2423 playing God of War Ragnarök for 50 viewers",
                    "showArt": false,
                    "showEmbed": true,
                    "showOverlay": true
                },
                "offlineDisplay": {
                    "title": "God of War Ragnarök",
                    "showArt": false,
                    "showEmbed": false,
                    "showOverlay": false
                },
                "itemType": "game",
                "rowName": "Top Games",
                "sortIndex": 0,
                "image": null,
                "overlay": "https://gamersx-prod-prod-us-west-1-storage.s3.us-west-1.amazonaws.com/pBvoC_Yq2K.jpg",
                "customArt": null,
                "link": "https://www.twitch.tv/directory/game/God of War Ragnarök",
                "componentName": "EmbedContainer",
                "embedName": "TwitchEmbed",
                "embedData": {
                    "channel": "cwells2423",
                    "elementId": "embed-64106d85e1cda"
                },
                "description": null,
                "isGlowStyling": "enabled_if_live"
            },
            {
                "info": {
                    "id": "23020",
                    "name": "ROBLOX",
                    "box_art_url": "https://static-cdn.jtvnw.net/ttv-boxart/23020_IGDB-{width}x{height}.jpg",
                    "igdb_id": "17269"
                },
                "broadcast": {
                    "id": "40563687128",
                    "user_id": "40034278",
                    "user_login": "varmilp",
                    "user_name": "VarmiLP",
                    "game_id": "23020",
                    "game_name": "ROBLOX",
                    "type": "live",
                    "title": "🔴ČEKÁME NA EVENT!🔥NEFUNKČNÍ MAILBOX?! ST. PATRICK EVENT V PET SIMULÁTORU X! SOUTĚŽE O HUGE!",
                    "viewer_count": 38,
                    "started_at": "2023-03-14T12:07:05Z",
                    "language": "cs",
                    "thumbnail_url": "https://static-cdn.jtvnw.net/previews-ttv/live_user_varmilp-{width}x{height}.jpg",
                    "tag_ids": [],
                    "tags": [
                        "Českárepublika",
                        "Hranísdiváky",
                        "PovolenéDrops",
                        "Čeština"
                    ],
                    "is_mature": false
                },
                "liveViewerCount": 38,
                "viewedCount": 0,
                "showOnline": true,
                "onlineDisplay": {
                    "title": "VarmiLP playing ROBLOX for 38 viewers",
                    "showArt": false,
                    "showEmbed": true,
                    "showOverlay": true
                },
                "offlineDisplay": {
                    "title": "ROBLOX",
                    "showArt": false,
                    "showEmbed": false,
                    "showOverlay": false
                },
                "itemType": "game",
                "rowName": "Top Games",
                "sortIndex": 0,
                "image": null,
                "overlay": "https://gamersx-prod-prod-us-west-1-storage.s3.us-west-1.amazonaws.com/vi-235p6Ad.jpg",
                "customArt": null,
                "link": "https://www.twitch.tv/directory/game/ROBLOX",
                "componentName": "EmbedContainer",
                "embedName": "TwitchEmbed",
                "embedData": {
                    "channel": "varmilp",
                    "elementId": "embed-64106d8647f44"
                },
                "description": null,
                "isGlowStyling": "enabled_if_live"
            },
            {
                "info": {
                    "id": "443735657",
                    "login": "zed_run",
                    "display_name": "zed_run",
                    "type": "",
                    "broadcaster_type": "",
                    "description": "ZED RUN is the first game of skill horse racing game to live not only on the blockchain but in an open market. ",
                    "profile_image_url": "https://static-cdn.jtvnw.net/jtv_user_pictures/aaff712e-aac9-450a-b649-98ec7fcc8bcb-profile_image-300x300.png",
                    "offline_image_url": "https://static-cdn.jtvnw.net/jtv_user_pictures/c6bb59c0-c761-47e0-bc33-ec01f648cf8c-channel_offline_image-1920x1080.png",
                    "view_count": 8666930,
                    "created_at": "2019-06-24T05:58:23Z"
                },
                "broadcast": {
                    "id": "41955817979",
                    "user_id": "443735657",
                    "user_login": "zed_run",
                    "user_name": "zed_run",
                    "game_id": "210533268",
                    "game_name": "ZED RUN",
                    "type": "live",
                    "title": "ZED RUN Live Stream",
                    "viewer_count": 1,
                    "started_at": "2023-03-13T08:24:52Z",
                    "language": "en",
                    "thumbnail_url": "https://static-cdn.jtvnw.net/previews-ttv/live_user_zed_run-{width}x{height}.jpg",
                    "tag_ids": [],
                    "tags": [
                        "HorseRacing",
                        "English"
                    ],
                    "is_mature": false
                },
                "liveViewerCount": 1,
                "viewedCount": 8666930,
                "showOnline": true,
                "onlineDisplay": {
                    "title": "zed_run playing ZED RUN for 1 viewers",
                    "showArt": false,
                    "showEmbed": true,
                    "showOverlay": true
                },
                "offlineDisplay": {
                    "title": "zed_run",
                    "showArt": false,
                    "showEmbed": false,
                    "showOverlay": true
                },
                "itemType": "streamer",
                "rowName": "Top Games",
                "sortIndex": null,
                "image": null,
                "overlay": "https://gamersx-prod-prod-us-west-1-storage.s3.us-west-1.amazonaws.com/gIsmyab54g.jpg",
                "customArt": null,
                "link": "https://www.twitch.tv/zed_run",
                "componentName": "EmbedContainer",
                "embedName": "TwitchEmbed",
                "embedData": {
                    "channel": "zed_run",
                    "elementId": "embed-64106d869bee7"
                },
                "description": null,
                "isGlowStyling": "enabled_if_live"
            }
        ]
    }
]
      },
      defaultSkeleton: "FullWidthDescriptiveSkeleton",
      cachedSkeletonRows: [],
      defaultSkeletonRows: [
        "FullWidthDescriptiveSkeleton",
        "ClassicSmSkeleton",
        "NumberedRowSkeleton",
        "ClassicMdSkeleton",
        "ParallaxSkeleton",
        "ClassicLgSkeleton",
        "ClassicVerticalSkeleton",
        "FullWidthImagerySkeleton",
      ],
      pollingApiData: null,
      requestPollingDelay: 90000};
  },
  methods: {
    requestHomeCachedRowsApi: function () {
      return axios.get("/home/rows/api")
        .catch(e => console.error(e))
        .then(response => {
          // this.settings.rows = [];
          if (response.data.settings.rows.length)
            this.cachedSkeletonRows = response.data.settings.rows;
        });
    },
    requestHomeApi: function () {
      // axios.get("/home/api")
      //   .catch(e => console.error(e))
      //   .then(response => {
      //     this.settings = response.data.settings;
      //   });
    }
  },
  mounted: function () {
    this.requestHomeCachedRowsApi().then(()=>{
      this.requestHomeApi();
    });
    this.pollingApiData = window.setInterval(() => {
      this.requestHomeApi();
    }, this.requestPollingDelay);
  },
  destroyed: function () {

    window.clearInterval(this.pollingApiData);
  }
};

/** We use this a lot for scrolling because JS % is remainder, not modulo **/
Number.prototype.mod = function (n) {
  return ((this % n) + n) % n;
};
</script>

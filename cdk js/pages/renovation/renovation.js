//index.js
const app = getApp()


Page({
    data: {
        index: 0,
        tmpName: '',
        btnconfirm: false,
        countAll: app.globalData.sum,
        unit: null,
        classify: ["厨房", "餐厅", "客厅", "主卧", "中卧", "小卧", "卫生间", "洗手间", "杂项", "自定义项目"],
        List: null,
        info: '',
        show: true,
        renovationName: '',
        info: '',
        tipType: ''
    },
    back: function() {
        wx.switchTab({
            url: '../../../../index/index'
        })
    },
    onLoad: function() {
        this.reset();
    },
    onShow: function() {

    },
    changeIndex: function(e) {
        this.setData({
            index: e.detail.value,
        })
    },
    findAndUpdateValue: function(List, classifyName, itemName, key, value) {
        var arr, num, price = null;
        var flag = 0;
        for (var i = 0; i < List.length; i++) {
            if (List[i].classifyName == classifyName) {
                var goods = List[i].goods;
                for (var j = 0; j < goods.length; j++) {
                    if (key == 'name') {
                        for (var s = 0; s < goods.length; s++) {
                            if (goods[s].name == value) {
                                flag++;
                            }
                        }
                        if (flag > 1) {
                            this.setData({
                                info: '命名重复，请重新输入...',
                                tipType: 'error'
                            })
                            flag = 0;
                        }

                        if (goods[j].addItem === true && this.data.btnconfirm == true) {
                            arr = ['List[' + i + '].goods[' + j + '].name', 'List[' + i + '].goods[' + j + '].addItem'];
                            this.setData({
                                [arr[0]]: value,
                                [arr[1]]: false,
                            })
                        }
                    } else if (this.isEqual(goods[j].name, itemName)) {

                        arr = 'List[' + i + '].goods[' + j + '].' + key;
                        this.setData({
                            [arr]: value,
                        });

                        num = List[i].goods[j].num;
                        price = List[i].goods[j].price;

                        if (List[i].goods[j].checked == true) {
                            if (!this.isEmpty(num) && !this.isEmpty(price)) {
                                this.Scount(List, i, j, num, price);
                            } else {
                                this.Scount(List, i, j, 0, 0);
                            }
                        } else {
                            this.setData({
                                info: '勾选框未勾选',
                                tipType: 'info'
                            })
                            arr = ['List[' + i + '].goods[' + j + '].count', 'List[' + i + '].goods[' + j + '].num',
                                'List[' + i + '].goods[' + j + '].price', 'List[' + i + '].goods[' + j + '].remark', 'List[' + i + '].isShow'
                            ];
                            this.setData({
                                [arr[0]]: "0.00",
                                [arr[1]]: "",
                                [arr[2]]: "",
                                [arr[3]]: ""
                            })
                        }
                    }
                    //
                }

            }

        }
        this.CountAllF(List);
    },
    bindchange: function(e) {
        this.setData({
            renovationName: e.detail.value
        })
    },
    confirm: function(e) {

        if (this.data.renovationName == '') {
            this.setData({
                info: '名称不能为空！',
                tipType: 'error'
            })
        } else {
            app.globalData.renovationName = this.data.renovationName;
            this.setData({
                show: false,
            })
        }

    },
    nameConfirm: function(e) {
        var classifyName = e.target.dataset.root[0];
        var itemName = e.target.dataset.root[1];
        this.setData({
            btnconfirm: true
        })
        this.findAndUpdateValue(this.data.List, classifyName, itemName, 'name', this.data.tmpName);
        this.setData({
            info: '已保存',
            tipType: 'success'
        })
    },
    isEqual: function(obj1, obj2) {

        if (obj1.length != obj2.length) {
            return false;
        }

        var flag = true;
        for (var i = 0; i < obj1.length; i++) {
            if (obj1[i] != obj2[i]) {
                flag = false;
            }
        }
        return flag;
    },
    isEmpty: function(obj) {
        if (typeof obj == "undefined" || obj == null || obj == "") {
            return true;
        } else {
            return false;
        }
    },
    Scount: function(List, i, j, num, price) {
        //小计
        var count = num * price;
        var arr = 'List[' + i + '].goods[' + j + '].count';
        if (count != null) {
            this.setData({
                [arr]: count.toFixed(2),
            })
        }
    },
    CountAllF: function(List) {
        var result = [];
        var tmp = 0.00;
        for (var i = 0; i < List.length; i++) {
            for (var j = 0; j < List[i].goods.length; j++) {
                if (List[i].goods[j].checked == true) {
                    result.push(List[i].goods[j]);
                }
            }
        }

        for (var k = 0; k < result.length; k++) {
            tmp += parseFloat(result[k].count);
        }

        this.setData({
            countAll: tmp.toFixed(2)
        });

        return tmp.toFixed(2);
    },
    setAttr: function(e) {
        var classifyName = e.target.dataset.root[0];
        var itemName = e.target.dataset.root[1];
        var key = e.target.dataset.id;
        var value = null;
        var unitIndex = null;
        var flag = true;

        if (key == 'name') {
            this.setData({
                tmpName: e.detail.value
            });
            flag = false;
        } else
        if (key == "unit") {
            unitIndex = e.detail.value;
            value = this.data.unit[unitIndex];
        } else if (key == "checked") {
            value = e.target.dataset.root[2] ? false : true;
        } else {
            value = e.detail.value;
        }
        if (flag) {
            this.findAndUpdateValue(this.data.List, classifyName, itemName, key, value)
        }
    },
    priview: function() {

        var List = this.data.List;
        var toplist = [];
        var x, y;
        for (var i = 0; i < List.length; i++) {
            for (var j = 0; j < List[i].goods.length; j++) {
                if (List[i].goods[j].checked != true) {
                    toplist.push([i, j]);
                }
            }

            for (var k = 0; k < toplist.length; k++) {
                x = toplist[k][0];
                y = toplist[k][1];
                List[x].goods.splice(y, 1, "")
            }
            toplist = [];
        }
        this.isShow(List);
        app.globalData.List = List;
        app.globalData.sum = this.CountAllF(List);

        wx.navigateTo({
            url: '../renovationPreview/renovationPreview'
        })

    },
    isShow: function(List) {
        var c = 0;
        var arr1 = null;
        for (var s = 0; s < List.length; s++) {
            for (var f = 0; f < List[s].goods.length; f++) {
                if (List[s].goods[f] == '') {
                    c++;
                }
            }
            if (List[s].goods.length != c) {
                arr1 = 'List[' + s + '].isShow';
                this.setData({
                    [arr1]: true,
                })
            }
            c = 0;
        }
    },
    cutGoods: function(e) {
        var id = e.target.dataset.id;
        var tmpList = this.data.List;
        var customID = app.globalData.customID;

        tmpList[customID].goods.splice(id, 1);

        this.setData({
            List: tmpList
        })

        this.setData({
            info: '已删除',
            tipType: 'success'
        })

    },
    addGoods: function(e) {
        //      --√--
        var customID = app.globalData.customID;
        var cnt = this.data.List[customID].goods.length;

        if (cnt == 0) {
            this.addAitem(customID, cnt);
            this.setData({
                info: '已添加',
                tipType: 'success'
            })
        } else {
            if (this.data.List[customID].goods[cnt - 1].name === '') {

                this.setData({
                    info: '请输入所有数据并确认',
                    tipType: 'error'
                })
            } else {
                this.addAitem(customID, cnt);
                this.setData({
                    info: '已添加',
                    tipType: 'success'
                })
            }
        }
    },
    addAitem: function(customID, cnt) {
        //      --√--
        var arr = ['List[' + customID + '].goods[' + cnt + '].name', 'List[' + customID + '].goods[' + cnt + '].unit',
            'List[' + customID + '].goods[' + cnt + '].num', 'List[' + customID + '].goods[' + cnt + '].price',
            'List[' + customID + '].goods[' + cnt + '].count', 'List[' + customID + '].goods[' + cnt + '].remark',
            'List[' + customID + '].goods[' + cnt + '].checked', 'List[' + customID + '].goods[' + cnt + '].addItem'
        ]

        this.setData({
            [arr[0]]: "",
            [arr[1]]: "kg（千克）",
            [arr[2]]: "",
            [arr[3]]: "",
            [arr[4]]: "0.00",
            [arr[5]]: "",
            [arr[6]]: true,
            [arr[7]]: true,
        });

    },
    reset: function(e) {

        var that = this;
        // List
        wx.request({

            url: 'https://renovation.12zw.club/modelJson',
            header: {
                'Content-Type': 'application/json'
            },
            success: function(res) {
                // console.log(res);
                // app.globalData.List = res.data;

                that.setData({
                    List: res.data
                });
            },
            fail: function(res) {
                this.setData({
                    info: '请求错误，请稍后再试',
                    tipType: 'error'
                })
            },
            complete: function() {

            }
        });

        // unit
        wx.request({
            url: 'https://renovation.12zw.club/getunit',
            header: {
                'Content-Type': 'application/json'
            },
            success: function(res) {
                // console.log(res);

                that.setData({
                    unit: res.data
                });
            },
            fail: function() {
                this.setData({
                    info: '请求错误，请稍后再试',
                    tipType: 'error'
                })
            },
            complete: function() {

            }
        });


        //customid
        wx.request({

            url: 'https://renovation.12zw.club/getCustomID',
            header: {
                'Content-Type': 'application/json'
            },
            method: 'GET',
            success: function(res) {

                app.globalData.customID = res.data;

                // console.log(app.globalData.customID);
            },
            fail: function() {
                this.setData({
                    info: '请求错误，请稍后再试',
                    tipType: 'error'
                })
            },
            complete: function() {

            }
        });
        // classify
        wx.request({
            url: 'https://renovation.12zw.club/getClassify',
            header: {
                'Content-Type': 'application/json'
            },
            success: function(res) {
                // console.log(res.data);
                that.setData({
                    classify: res.data
                });
            },
            fail: function(res) {
                this.setData({
                    info: '请求错误，请稍后再试',
                    tipType: 'error'
                })
            },
            complete: function() {

            }
        });

        that.setData({
            // List: app.globalData.List,
            countAll: "0.00"
        })

    }
})
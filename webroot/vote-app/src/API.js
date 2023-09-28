class API {
  static getBasePath() {
    if (window.location.host === 'localhost:3000') {
      return 'http://vore.test:9000';
    }
    return '';
  }

  static async getProjects(setErrorMsg) {
    //return this.getDummyProjects();

    let retval = null;
    const url = this.getBasePath() + '/api/projects';
    const fetchOptions = {
      headers: {'Content-Type': 'application/json'}
    };
    try {
      const response = await fetch(url, fetchOptions);
      const data = await response.json();
      retval = data.projects;
    } catch(error) {
      console.error('Error:', error);
      setErrorMsg(error);
    }
    return retval;
  }

  static async postVotes(data) {
    data.fundingCycleId = window.fundingCycleId;
    const isDevMode = !process.env.NODE_ENV || process.env.NODE_ENV === 'development';
    const urlBase = isDevMode ? 'http://vore.test:9000' : '';
    const url = urlBase + '/api/votes';
    const fetchOptions = {
      method: 'POST',
      headers: {'Content-Type': 'application/x-www-form-urlencoded'},
      body: JSON.stringify(data),
    };
    const response = await fetch(url, fetchOptions);
    const responseJson = await response.json();
    if (responseJson?.result) {
      console.log('Success:', responseJson);
      return true;
    }
    console.error('Error response:', responseJson);
    return false;
  }

  static getDummyProjects() {
    return [
      {
        "accept_partial_payout": true,
        "amount_requested": 123,
        "category_id": 6,
        "description": "dsf",
        "id": 15,
        "title": "2",
        "user_id": 3,
        "user": {
          "id": 3,
          "name": "Graham Watson"
        },
        "images": [],
        "category": {
          "id": 6,
          "name": "Film"
        },
        "answers": [
          {
            "answer": "dsf",
            "project_id": 15,
            "id": 69,
            "question_id": 1,
            "question": {
              "id": 1,
              "question": "What expenses do you need help covering?",
              "weight": 0
            }
          },
          {
            "answer": "dfs",
            "project_id": 15,
            "id": 70,
            "question_id": 2,
            "question": {
              "id": 2,
              "question": "How will your project generate money?",
              "weight": 1
            }
          },
          {
            "answer": "dfs",
            "project_id": 15,
            "id": 71,
            "question_id": 3,
            "question": {
              "id": 3,
              "question": "When do you expect to be able to repay this loan?",
              "weight": 2
            }
          },
          {
            "answer": "fds",
            "project_id": 15,
            "id": 72,
            "question_id": 4,
            "question": {
              "id": 4,
              "question": "Do you have experience or support that will help this project succeed?",
              "weight": 3
            }
          }
        ]
      },
      {
        "accept_partial_payout": true,
        "amount_requested": 123,
        "category_id": 4,
        "description": "dsf",
        "id": 16,
        "title": "4",
        "user_id": 3,
        "user": {
          "id": 3,
          "name": "Graham Watson"
        },
        "images": [],
        "category": {
          "id": 4,
          "name": "Literature"
        },
        "answers": [
          {
            "answer": "df",
            "project_id": 16,
            "id": 73,
            "question_id": 1,
            "question": {
              "id": 1,
              "question": "What expenses do you need help covering?",
              "weight": 0
            }
          },
          {
            "answer": "dsf",
            "project_id": 16,
            "id": 74,
            "question_id": 2,
            "question": {
              "id": 2,
              "question": "How will your project generate money?",
              "weight": 1
            }
          },
          {
            "answer": "dfs",
            "project_id": 16,
            "id": 75,
            "question_id": 3,
            "question": {
              "id": 3,
              "question": "When do you expect to be able to repay this loan?",
              "weight": 2
            }
          },
          {
            "answer": "sdf",
            "project_id": 16,
            "id": 76,
            "question_id": 4,
            "question": {
              "id": 4,
              "question": "Do you have experience or support that will help this project succeed?",
              "weight": 3
            }
          }
        ]
      },
      {
        "accept_partial_payout": true,
        "amount_requested": 123,
        "category_id": 4,
        "description": "dsf",
        "id": 17,
        "title": "4",
        "user_id": 3,
        "user": {
          "id": 3,
          "name": "Graham Watson"
        },
        "images": [
          {
            "project_id": 17,
            "filename": "dca8059f5b.png",
            "id": 2,
            "weight": 0
          },
          {
            "project_id": 17,
            "filename": "42b6217b66.png",
            "id": 3,
            "weight": 0
          },
          {
            "project_id": 17,
            "filename": "de79b155a1.png",
            "id": 4,
            "weight": 0
          }
        ],
        "category": {
          "id": 4,
          "name": "Literature"
        },
        "answers": [
          {
            "answer": "df",
            "project_id": 17,
            "id": 77,
            "question_id": 1,
            "question": {
              "id": 1,
              "question": "What expenses do you need help covering?",
              "weight": 0
            }
          },
          {
            "answer": "dsf",
            "project_id": 17,
            "id": 78,
            "question_id": 2,
            "question": {
              "id": 2,
              "question": "How will your project generate money?",
              "weight": 1
            }
          },
          {
            "answer": "dfs",
            "project_id": 17,
            "id": 79,
            "question_id": 3,
            "question": {
              "id": 3,
              "question": "When do you expect to be able to repay this loan?",
              "weight": 2
            }
          },
          {
            "answer": "sdf",
            "project_id": 17,
            "id": 80,
            "question_id": 4,
            "question": {
              "id": 4,
              "question": "Do you have experience or support that will help this project succeed?",
              "weight": 3
            }
          }
        ]
      },
      {
        "accept_partial_payout": true,
        "amount_requested": 78978,
        "category_id": 6,
        "description": "dsf",
        "id": 13,
        "title": "dfsa",
        "user_id": 3,
        "user": {
          "id": 3,
          "name": "Graham Watson"
        },
        "images": [],
        "category": {
          "id": 6,
          "name": "Film"
        },
        "answers": [
          {
            "answer": "dsg",
            "project_id": 13,
            "id": 61,
            "question_id": 1,
            "question": {
              "id": 1,
              "question": "What expenses do you need help covering?",
              "weight": 0
            }
          },
          {
            "answer": "gdfs",
            "project_id": 13,
            "id": 62,
            "question_id": 2,
            "question": {
              "id": 2,
              "question": "How will your project generate money?",
              "weight": 1
            }
          },
          {
            "answer": "dsf",
            "project_id": 13,
            "id": 63,
            "question_id": 3,
            "question": {
              "id": 3,
              "question": "When do you expect to be able to repay this loan?",
              "weight": 2
            }
          },
          {
            "answer": "sdf",
            "project_id": 13,
            "id": 64,
            "question_id": 4,
            "question": {
              "id": 4,
              "question": "Do you have experience or support that will help this project succeed?",
              "weight": 3
            }
          }
        ]
      },
      {
        "accept_partial_payout": false,
        "amount_requested": 123,
        "category_id": 6,
        "description": "ewf",
        "id": 9,
        "title": "dsf",
        "user_id": 3,
        "user": {
          "id": 3,
          "name": "Graham Watson"
        },
        "images": [],
        "category": {
          "id": 6,
          "name": "Film"
        },
        "answers": [
          {
            "answer": "few",
            "project_id": 9,
            "id": 45,
            "question_id": 1,
            "question": {
              "id": 1,
              "question": "What expenses do you need help covering?",
              "weight": 0
            }
          },
          {
            "answer": "efw",
            "project_id": 9,
            "id": 46,
            "question_id": 2,
            "question": {
              "id": 2,
              "question": "How will your project generate money?",
              "weight": 1
            }
          },
          {
            "answer": "ewf",
            "project_id": 9,
            "id": 47,
            "question_id": 3,
            "question": {
              "id": 3,
              "question": "When do you expect to be able to repay this loan?",
              "weight": 2
            }
          },
          {
            "answer": "wef",
            "project_id": 9,
            "id": 48,
            "question_id": 4,
            "question": {
              "id": 4,
              "question": "Do you have experience or support that will help this project succeed?",
              "weight": 3
            }
          }
        ]
      },
      {
        "accept_partial_payout": false,
        "amount_requested": 213,
        "category_id": 6,
        "description": "sdf",
        "id": 8,
        "title": "FEerfafsad",
        "user_id": 3,
        "user": {
          "id": 3,
          "name": "Graham Watson"
        },
        "images": [],
        "category": {
          "id": 6,
          "name": "Film"
        },
        "answers": [
          {
            "answer": "dsf",
            "project_id": 8,
            "id": 41,
            "question_id": 1,
            "question": {
              "id": 1,
              "question": "What expenses do you need help covering?",
              "weight": 0
            }
          },
          {
            "answer": "dsfg",
            "project_id": 8,
            "id": 42,
            "question_id": 2,
            "question": {
              "id": 2,
              "question": "How will your project generate money?",
              "weight": 1
            }
          },
          {
            "answer": "dsf",
            "project_id": 8,
            "id": 43,
            "question_id": 3,
            "question": {
              "id": 3,
              "question": "When do you expect to be able to repay this loan?",
              "weight": 2
            }
          },
          {
            "answer": "sdf",
            "project_id": 8,
            "id": 44,
            "question_id": 4,
            "question": {
              "id": 4,
              "question": "Do you have experience or support that will help this project succeed?",
              "weight": 3
            }
          }
        ]
      },
      {
        "accept_partial_payout": false,
        "amount_requested": 234,
        "category_id": 2,
        "description": "sd fds\r\ndsf ds\r\ndsf\r\ndsf\r\ndfs",
        "id": 3,
        "title": "Flarp",
        "user_id": 3,
        "user": {
          "id": 3,
          "name": "Graham Watson"
        },
        "images": [],
        "category": {
          "id": 2,
          "name": "Sculpture"
        },
        "answers": []
      },
      {
        "accept_partial_payout": false,
        "amount_requested": 231,
        "category_id": 4,
        "description": "sd f",
        "id": 10,
        "title": "hgfdhgdf gfd",
        "user_id": 3,
        "user": {
          "id": 3,
          "name": "Graham Watson"
        },
        "images": [],
        "category": {
          "id": 4,
          "name": "Literature"
        },
        "answers": [
          {
            "answer": "ds f",
            "project_id": 10,
            "id": 49,
            "question_id": 1,
            "question": {
              "id": 1,
              "question": "What expenses do you need help covering?",
              "weight": 0
            }
          },
          {
            "answer": "ds f",
            "project_id": 10,
            "id": 50,
            "question_id": 2,
            "question": {
              "id": 2,
              "question": "How will your project generate money?",
              "weight": 1
            }
          },
          {
            "answer": "ds f",
            "project_id": 10,
            "id": 51,
            "question_id": 3,
            "question": {
              "id": 3,
              "question": "When do you expect to be able to repay this loan?",
              "weight": 2
            }
          },
          {
            "answer": "ds f",
            "project_id": 10,
            "id": 52,
            "question_id": 4,
            "question": {
              "id": 4,
              "question": "Do you have experience or support that will help this project succeed?",
              "weight": 3
            }
          }
        ]
      }
    ];
  }
}

export default API;
